@extends('layouts.app')

@section('content')

        <div id="topology-container" class="w-full bg-white dark:bg-gray-800" :class="isFullscreen ? 'fixed inset-0 z-[100] h-screen w-screen overflow-hidden rounded-none' : ''" x-data="topologyDesigner()" x-init="init()" @keydown.delete.window="if(isEditMode) deleteSelected()" @keydown.escape.window="cancelConnection()" @keydown.window.ctrl.z.prevent="if(isEditMode) undo()" @keydown.window.ctrl.y.prevent="if(isEditMode) undo()">

            <!-- Empty State View -->
            <div x-show="!hasTopologyData" class="flex flex-col items-center justify-center min-h-[calc(100vh-100px)] bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700/60 p-8 text-center" style="display: none;">
                <div class="w-20 h-20 bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl flex items-center justify-center mb-6 text-indigo-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Belum Ada Topologi</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">Anda belum membuat desain jaringan untuk topologi ini. Mulai tambahkan server, panel, dan switch sekarang juga.</p>
                <button @click="hasTopologyData = true; isEditMode = true" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold shadow-lg shadow-indigo-500/30 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Topologi
                </button>
            </div>

            <!-- Main View -->
            <div x-show="hasTopologyData" style="display: none;">

            {{-- ═══════════════════════════════════════════════════ --}}
            {{-- HEADER BAR --}}
            {{-- ═══════════════════════════════════════════════════ --}}
            <div class="bg-white dark:bg-gray-800 rounded-t-2xl shadow-sm border border-b-0 border-gray-200 dark:border-gray-700/60">
                <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-3 px-5 py-3">
                    <!-- Title & Metadata -->
                    <div class="flex items-center gap-4 flex-shrink-0">
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="6" cy="6" r="2.25" stroke-width="1.5"/><circle cx="18" cy="6" r="2.25" stroke-width="1.5"/><circle cx="12" cy="12" r="2.25" stroke-width="1.5"/><circle cx="6" cy="18" r="2.25" stroke-width="1.5"/><circle cx="18" cy="18" r="2.25" stroke-width="1.5"/><path d="M7.6 7.6L10.4 10.4M13.6 10.4L16.4 7.6M10.4 13.6L7.6 16.4M13.6 13.6L16.4 16.4" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </div>
                        <div>
                            <input type="text" x-model="topologyName" placeholder="Nama Topologi..." :readonly="!isEditMode"
                                   class="bg-transparent border-0 border-b border-transparent hover:border-gray-300 dark:hover:border-gray-600 focus:border-indigo-500 focus:ring-0 px-0 py-0 text-lg font-bold text-gray-900 dark:text-white placeholder-gray-400 transition-colors w-64">
                            <div class="flex items-center mt-1">
                                <input type="date" x-model="topologyDate" :readonly="!isEditMode"
                                       class="bg-transparent border-0 border-b border-transparent hover:border-gray-300 dark:hover:border-gray-600 focus:border-indigo-500 focus:ring-0 px-0 py-0 text-xs text-gray-500 dark:text-gray-400 transition-colors w-32 cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <!-- Toolbar Actions -->
                    <div class="flex items-center gap-2">
                        <!-- Zoom Controls -->
                        <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700/50 rounded-lg p-1">
                            <button @click="zoomOut()" class="p-1.5 rounded-md text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-white dark:hover:bg-gray-600 transition-colors" title="Zoom Out">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/></svg>
                            </button>
                            <span class="text-xs font-mono font-semibold text-gray-600 dark:text-gray-300 min-w-[40px] text-center" x-text="Math.round(zoom * 100) + '%'"></span>
                            <button @click="zoomIn()" class="p-1.5 rounded-md text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-white dark:hover:bg-gray-600 transition-colors" title="Zoom In">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                            </button>
                        </div>

                        <div class="w-px h-6 bg-gray-200 dark:bg-gray-700"></div>

                        <!-- Undo / Redo -->
                        <div x-show="isEditMode" class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700/50 rounded-lg p-1 mr-2">
                            <button @click="undo()" :disabled="history.length === 0" :class="history.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-white dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white'" class="p-1.5 rounded-md text-gray-500 dark:text-gray-400 transition-colors" title="Undo (Ctrl+Z)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            </button>
                            <button @click="redo()" :disabled="redoStack.length === 0" :class="redoStack.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-white dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white'" class="p-1.5 rounded-md text-gray-500 dark:text-gray-400 transition-colors" title="Redo (Ctrl+Y)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"/></svg>
                            </button>
                        </div>

                        <!-- Add Waypoint Button -->
                        <button x-show="isEditMode && selectedConnection" @click="addWaypointToSelected()" 
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors"
                            title="Tambah Waypoint di tengah garis">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Waypoint</span>
                        </button>

                        <!-- Beri Label -->
                <button x-show="isEditMode && selectedConnection" @click="openEditModal(selectedConnection, 'connection')" 
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors"
                    title="Beri nama label pada garis">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                    <span>Beri Label</span>
                </button>

                <!-- Clear All -->
                <button x-show="isEditMode && !selectedNode && !selectedConnection" @click="clearAll()" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-100 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors" title="Hapus Semua">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Hapus Semua</span>
                </button>

                <!-- Delete Selected -->
                <button x-show="isEditMode && selectedConnection" @click="deleteSelectedConnection()" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors" title="Hapus Garis">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Hapus Garis</span>
                </button>
                <button x-show="isEditMode && selectedNode" @click="deleteSelected()" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors" title="Hapus Komponen">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Hapus Komponen</span>
                </button>

                <!-- Live Ping -->
                <button @click="livePing()" class="flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-sm font-semibold bg-blue-500 text-white hover:bg-blue-600 shadow-sm shadow-blue-500/25 transition-colors" :disabled="isPinging" :class="isPinging ? 'opacity-70 cursor-not-allowed' : ''">
                    <svg x-show="!isPinging" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <svg x-show="isPinging" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span x-text="isPinging ? 'Pinging...' : 'Live Ping'"></span>
                </button>

                <!-- Fullscreen -->
                <button @click="toggleFullscreen()" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-100 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" title="Toggle Fullscreen">
                    <svg x-show="!isFullscreen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    <svg x-show="isFullscreen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 14h4v4M4 14l5 5m11-5h-4v4m4-4l-5 5M4 10h4V6m-4 4l5-5m11 4h-4V6m4 4l-5-5"/></svg>
                    <span x-text="isFullscreen ? 'Keluar' : 'Fullscreen'"></span>
                </button>

                <!-- Save/Edit Actions -->
                <button x-show="!isEditMode" @click="isEditMode = true; selectedNode = null; selectedConnection = null;" class="flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-sm font-semibold bg-amber-500 text-white hover:bg-amber-600 shadow-sm shadow-amber-500/25 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <span>Edit Topologi</span>
                </button>

                <button x-show="isEditMode" @click="saveTopology(); isEditMode = false; selectedNode = null; selectedConnection = null;" class="flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-sm font-semibold bg-indigo-500 text-white hover:bg-indigo-600 shadow-sm shadow-indigo-500/25 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    <span>Simpan & Selesai</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- MAIN WORKSPACE --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <div class="flex border border-t-0 border-gray-200 dark:border-gray-700/60 rounded-b-2xl overflow-hidden" :style="isFullscreen ? 'height: calc(100vh - 76px);' : 'height: calc(100vh - 210px);'">

        {{-- ─── LEFT: Component Palette ─── --}}
        <div x-show="isEditMode" style="display: none;" class="w-56 flex-shrink-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700/60 flex flex-col">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Komponen</h2>
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-2">
                
                {{-- Server Pusat --}}
                <div class="topology-palette-item group cursor-grab active:cursor-grabbing rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-600 hover:border-indigo-300 dark:hover:border-indigo-500 p-3 transition-all duration-200 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/10"
                     draggable="true"
                     @dragstart="onPaletteDragStart($event, 'server')"
                     @dragend="isDraggingOver = false">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">Server Pusat</div>
                            <div class="text-[10px] text-gray-400 dark:text-gray-500">Main Server / Core</div>
                        </div>
                    </div>
                </div>

                {{-- Panel --}}
                <div class="topology-palette-item group cursor-grab active:cursor-grabbing rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-600 hover:border-slate-400 dark:hover:border-slate-500 p-3 transition-all duration-200 hover:bg-slate-50/50 dark:hover:bg-slate-900/10"
                     draggable="true"
                     @dragstart="onPaletteDragStart($event, 'panel')"
                     @dragend="isDraggingOver = false">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-slate-500 to-slate-600 flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">Panel</div>
                            <div class="text-[10px] text-gray-400 dark:text-gray-500">Berisi beberapa switch</div>
                        </div>
                    </div>
                </div>

                {{-- Switch --}}
                <div class="topology-palette-item group cursor-grab active:cursor-grabbing rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-600 hover:border-emerald-300 dark:hover:border-emerald-500 p-3 transition-all duration-200 hover:bg-emerald-50/50 dark:hover:bg-emerald-900/10"
                     draggable="true"
                     @dragstart="onPaletteDragStart($event, 'switch')"
                     @dragend="isDraggingOver = false">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"><rect x="3" y="8" width="18" height="8" rx="2" stroke="currentColor" stroke-width="1.5"/><line x1="7" y1="11" x2="7" y2="13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><line x1="10" y1="11" x2="10" y2="13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><line x1="13" y1="11" x2="13" y2="13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><line x1="16" y1="11" x2="16" y2="13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">Switch</div>
                            <div class="text-[10px] text-gray-400 dark:text-gray-500">Drop ke dalam Panel</div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Legend --}}
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700/60 space-y-1.5">
                <h3 class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2">Status</h3>
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 shadow-sm shadow-green-500/30"></span> Online
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 shadow-sm shadow-red-500/30"></span> Offline
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <span class="w-2.5 h-2.5 rounded-full bg-gray-400 shadow-sm"></span> Belum Dicek
                </div>
            </div>

            {{-- Tips --}}
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700/60">
                <div class="text-[10px] text-gray-400 dark:text-gray-500 space-y-1">
                    <p><kbd class="px-1 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-[9px] font-mono">2x Klik</kbd> Edit komponen</p>
                    <p><kbd class="px-1 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-[9px] font-mono">Del</kbd> Hapus yang dipilih</p>
                    <p><kbd class="px-1 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-[9px] font-mono">Esc</kbd> Batalkan koneksi</p>
                    <p><kbd class="px-1 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-[9px] font-mono">Scroll</kbd> Zoom in/out</p>
                </div>
            </div>
        </div>

        {{-- ─── RIGHT: Canvas Area ─── --}}
        <div class="flex-1 relative overflow-hidden bg-gray-50 dark:bg-gray-900"
             id="canvas-wrapper"
             @drop.prevent="onCanvasDrop($event)"
             @dragover.prevent="onCanvasDragOver($event)"
             @dragleave.self="isDraggingOver = false"
             @mousedown="onCanvasMouseDown($event)"
             @mousemove.window="onCanvasMouseMove($event)"
             @mouseup.window="onCanvasMouseUp($event)"
             @wheel.prevent="onCanvasWheel($event)">

            {{-- Grid Background --}}
            <div class="absolute inset-0 pointer-events-none" 
                 :style="`background-image: radial-gradient(circle, ${$store.theme.theme === 'dark' ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.07)'} 1px, transparent 1px); background-size: ${20 * zoom}px ${20 * zoom}px; background-position: ${panX % (20*zoom)}px ${panY % (20*zoom)}px;`">
            </div>

            {{-- Canvas Connection Lines Layer --}}
            <canvas id="connections-canvas" class="absolute inset-0 w-full h-full" style="z-index: 1; pointer-events: none;"
                    x-effect="connections; servers; panels; zoom; panX; panY; selectedConnection; isDrawingConnection; drawLineFrom; drawLineTo; $nextTick(() => drawConnections())">
            </canvas>

            {{-- Canvas Transform Layer --}}
            <div id="canvas-transform"
                 class="absolute origin-top-left"
                 :style="`transform: translate(${panX}px, ${panY}px) scale(${zoom}); width: 5000px; height: 5000px;`">

                {{-- ── Server Nodes ── --}}
                <template x-for="server in servers" :key="server.id">
                    <div class="absolute group"
                         :id="'node-' + server.id"
                         :style="`left: ${server.x}px; top: ${server.y}px; z-index: ${selectedNode === server.id ? 30 : 10};`"
                         @mousedown.stop="startDragNode($event, server.id, 'server')"
                         @click.stop="selectNode(server.id, 'server')"
                         @dblclick.stop="openEditModal(server.id, 'server')">
                        
                        {{-- Server Visual --}}
                        <div class="w-[160px] rounded-xl border-2 transition-all duration-200 shadow-lg"
                             :class="selectedNode === server.id 
                                ? 'border-indigo-500 shadow-indigo-500/20 ring-2 ring-indigo-500/20' 
                                : 'border-blue-200 dark:border-blue-800 shadow-blue-500/5 hover:shadow-blue-500/15'">
                            
                            {{-- Server Header --}}
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-t-[10px] px-3 py-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                                <span class="text-xs font-bold text-white truncate" x-text="server.name"></span>
                            </div>
                            
                            {{-- Server Body (SVG Illustration) --}}
                            <div class="bg-white dark:bg-gray-800 rounded-b-[10px] p-3 flex flex-col items-center gap-2">
                                <svg viewBox="0 0 120 80" class="w-24 h-16">
                                    <!-- Server rack body -->
                                    <rect x="10" y="2" width="100" height="24" rx="3" fill="#3b82f6" opacity="0.15" stroke="#3b82f6" stroke-width="1.5"/>
                                    <rect x="10" y="28" width="100" height="24" rx="3" fill="#3b82f6" opacity="0.1" stroke="#3b82f6" stroke-width="1.5"/>
                                    <rect x="10" y="54" width="100" height="24" rx="3" fill="#3b82f6" opacity="0.05" stroke="#3b82f6" stroke-width="1.5"/>
                                    <!-- Drive bays -->
                                    <rect x="18" y="8" width="30" height="4" rx="1" fill="#3b82f6" opacity="0.4"/>
                                    <rect x="18" y="14" width="30" height="4" rx="1" fill="#3b82f6" opacity="0.3"/>
                                    <rect x="18" y="34" width="30" height="4" rx="1" fill="#3b82f6" opacity="0.3"/>
                                    <rect x="18" y="40" width="30" height="4" rx="1" fill="#3b82f6" opacity="0.2"/>
                                    <rect x="18" y="60" width="30" height="4" rx="1" fill="#3b82f6" opacity="0.2"/>
                                    <rect x="18" y="66" width="30" height="4" rx="1" fill="#3b82f6" opacity="0.15"/>
                                    <!-- Status LEDs -->
                                    <circle cx="95" cy="10" r="2.5" :fill="server.status === 'online' ? '#22c55e' : (server.status === 'offline' ? '#ef4444' : '#9ca3af')">
                                        <template x-if="server.status === 'online'">
                                            <animate attributeName="opacity" values="1;0.4;1" dur="2s" repeatCount="indefinite"/>
                                        </template>
                                    </circle>
                                    <circle cx="95" cy="18" r="2.5" fill="#3b82f6" opacity="0.5"/>
                                    <circle cx="95" cy="36" r="2.5" :fill="server.status === 'online' ? '#22c55e' : '#9ca3af'" opacity="0.7"/>
                                    <circle cx="95" cy="62" r="2.5" fill="#3b82f6" opacity="0.3"/>
                                </svg>
                                <div class="text-[10px] text-gray-400 dark:text-gray-500 font-mono" x-text="server.ip || 'Belum diatur'"></div>
                                {{-- Status indicator --}}
                                <div class="flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full"
                                          :class="{
                                              'bg-green-500 shadow-sm shadow-green-500/50': server.status === 'online',
                                              'bg-red-500 shadow-sm shadow-red-500/50': server.status === 'offline',
                                              'bg-gray-400': server.status === 'pending'
                                          }"></span>
                                    <span class="text-[10px] font-semibold uppercase"
                                          :class="{
                                              'text-green-600 dark:text-green-400': server.status === 'online',
                                              'text-red-600 dark:text-red-400': server.status === 'offline',
                                              'text-gray-400': server.status === 'pending'
                                          }"
                                          x-text="server.status === 'online' ? 'Online' : (server.status === 'offline' ? 'Offline' : 'Pending')"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Connection anchor points (4 edges) --}}
                        <div>
                            {{-- Top --}}
                            <div class="absolute -top-2 left-1/2 -translate-x-1/2 w-3.5 h-3.5 rounded-full bg-indigo-500 border-2 border-white dark:border-gray-900 shadow-lg cursor-crosshair z-40 transition-all duration-200"
                                 :class="(connectionMode || isDrawingConnection) ? 'opacity-70 hover:opacity-100 hover:scale-150 pointer-events-auto' : 'opacity-0 pointer-events-none group-hover:pointer-events-auto group-hover:opacity-70 hover:!opacity-100 hover:!scale-150'"
                                 @click.stop="handleAnchorClick(server.id, 'server', 'top')"></div>
                            {{-- Bottom --}}
                            <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-3.5 h-3.5 rounded-full bg-indigo-500 border-2 border-white dark:border-gray-900 shadow-lg cursor-crosshair z-40 transition-all duration-200"
                                 :class="(connectionMode || isDrawingConnection) ? 'opacity-70 hover:opacity-100 hover:scale-150 pointer-events-auto' : 'opacity-0 pointer-events-none group-hover:pointer-events-auto group-hover:opacity-70 hover:!opacity-100 hover:!scale-150'"
                                 @click.stop="handleAnchorClick(server.id, 'server', 'bottom')"></div>
                            {{-- Left --}}
                            <div class="absolute top-1/2 -left-2 -translate-y-1/2 w-3.5 h-3.5 rounded-full bg-indigo-500 border-2 border-white dark:border-gray-900 shadow-lg cursor-crosshair z-40 transition-all duration-200"
                                 :class="(connectionMode || isDrawingConnection) ? 'opacity-70 hover:opacity-100 hover:scale-150 pointer-events-auto' : 'opacity-0 pointer-events-none group-hover:pointer-events-auto group-hover:opacity-70 hover:!opacity-100 hover:!scale-150'"
                                 @click.stop="handleAnchorClick(server.id, 'server', 'left')"></div>
                            {{-- Right --}}
                            <div class="absolute top-1/2 -right-2 -translate-y-1/2 w-3.5 h-3.5 rounded-full bg-indigo-500 border-2 border-white dark:border-gray-900 shadow-lg cursor-crosshair z-40 transition-all duration-200"
                                 :class="(connectionMode || isDrawingConnection) ? 'opacity-70 hover:opacity-100 hover:scale-150 pointer-events-auto' : 'opacity-0 pointer-events-none group-hover:pointer-events-auto group-hover:opacity-70 hover:!opacity-100 hover:!scale-150'"
                                 @click.stop="handleAnchorClick(server.id, 'server', 'right')"></div>
                        </div>
                    </div>
                </template>

                {{-- ── Panel Nodes ── --}}
                <template x-for="panel in panels" :key="panel.id">
                    <div class="absolute group"
                         :id="'node-' + panel.id"
                         :style="`left: ${panel.x}px; top: ${panel.y}px; z-index: ${selectedNode === panel.id ? 30 : 5};`"
                         @mousedown.stop="startDragNode($event, panel.id, 'panel')"
                         @click.stop="selectNode(panel.id, 'panel')"
                         @dblclick.stop="openEditModal(panel.id, 'panel')"
                         @drop.stop="onSwitchDropToPanel($event, panel.id)"
                         @dragover.prevent.stop="onPanelDragOver($event, panel.id)"
                         @dragleave="onPanelDragLeave(panel.id)">
                        
                        {{-- Panel Visual --}}
                        <div class="rounded-xl border-2 transition-all duration-200 shadow-lg min-w-[200px]"
                             :class="{ 
                                 'border-indigo-500 shadow-indigo-500/20 ring-2 ring-indigo-500/20': selectedNode === panel.id,
                                 'border-slate-300 dark:border-slate-600 shadow-slate-500/5 hover:shadow-slate-500/15': selectedNode !== panel.id && !panel.dragOver,
                                 'border-emerald-400 dark:border-emerald-600 shadow-emerald-500/20 ring-2 ring-emerald-500/20 scale-[1.02]': panel.dragOver
                             }">
                            
                            {{-- Panel Header --}}
                            <div class="bg-gradient-to-r from-slate-500 to-slate-600 rounded-t-[10px] px-3 py-2 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    <span class="text-xs font-bold text-white truncate" x-text="panel.name"></span>
                                </div>
                                <span class="text-[10px] text-white/70 font-mono" x-text="panel.switches.length + ' switch'"></span>
                            </div>
                            
                            {{-- Panel Body --}}
                            <div class="bg-white dark:bg-gray-800 rounded-b-[10px] p-3">
                                {{-- Switches inside panel --}}
                                <div class="space-y-1.5 min-h-[60px]" 
                                     :class="panel.switches.length === 0 ? 'flex items-center justify-center' : ''">
                                    
                                    {{-- Empty state --}}
                                    <template x-if="panel.switches.length === 0">
                                        <div class="text-center py-2">
                                            <svg class="w-8 h-8 mx-auto text-gray-300 dark:text-gray-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                            <p class="text-[10px] text-gray-400 dark:text-gray-500">Drop switch di sini</p>
                                        </div>
                                    </template>

                                    {{-- Switch items --}}
                                    <template x-for="sw in panel.switches" :key="sw.id">
                                        <div class="relative group/switch rounded-lg border transition-all duration-150 cursor-pointer"
                                             :id="'node-' + sw.id"
                                             :class="{
                                                'border-indigo-300 dark:border-indigo-600 bg-indigo-50/50 dark:bg-indigo-900/20': selectedNode === sw.id,
                                                'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 hover:border-gray-300 dark:hover:border-gray-600': selectedNode !== sw.id
                                             }"
                                             @mousedown.stop="startDragNode($event, sw.id, 'switch')"
                                             @click.stop="selectNode(sw.id, 'switch')"
                                             @dblclick.stop="openEditModal(sw.id, 'switch')">
                                            <div class="px-2.5 py-2 flex items-center gap-2.5">
                                                {{-- Switch SVG Icon --}}
                                                <div class="flex-shrink-0">
                                                    <svg viewBox="0 0 64 32" class="w-12 h-6">
                                                        <!-- Switch body -->
                                                        <rect x="1" y="4" width="62" height="24" rx="4" 
                                                              :fill="sw.status === 'online' ? '#dcfce7' : (sw.status === 'offline' ? '#fee2e2' : '#f3f4f6')"
                                                              :stroke="sw.status === 'online' ? '#22c55e' : (sw.status === 'offline' ? '#ef4444' : '#d1d5db')"
                                                              stroke-width="1.5"/>
                                                        <!-- Ports -->
                                                        <rect x="8" y="10" width="5" height="7" rx="1" :fill="sw.status === 'online' ? '#22c55e' : '#9ca3af'" opacity="0.6"/>
                                                        <rect x="15" y="10" width="5" height="7" rx="1" :fill="sw.status === 'online' ? '#22c55e' : '#9ca3af'" opacity="0.5"/>
                                                        <rect x="22" y="10" width="5" height="7" rx="1" :fill="sw.status === 'online' ? '#22c55e' : '#9ca3af'" opacity="0.4"/>
                                                        <rect x="29" y="10" width="5" height="7" rx="1" :fill="sw.status === 'online' ? '#22c55e' : '#9ca3af'" opacity="0.5"/>
                                                        <rect x="36" y="10" width="5" height="7" rx="1" :fill="sw.status === 'online' ? '#22c55e' : '#9ca3af'" opacity="0.4"/>
                                                        <rect x="43" y="10" width="5" height="7" rx="1" :fill="sw.status === 'online' ? '#22c55e' : '#9ca3af'" opacity="0.3"/>
                                                        <!-- Status LED -->
                                                        <circle cx="55" cy="13" r="2" :fill="sw.status === 'online' ? '#22c55e' : (sw.status === 'offline' ? '#ef4444' : '#9ca3af')">
                                                            <template x-if="sw.status === 'online'">
                                                                <animate attributeName="opacity" values="1;0.3;1" dur="1.5s" repeatCount="indefinite"/>
                                                            </template>
                                                        </circle>
                                                        <!-- Brand text -->
                                                        <text x="32" y="26" text-anchor="middle" fill="#9ca3af" font-size="4.5" font-family="monospace" x-text="sw.merk || 'Switch'"></text>
                                                    </svg>
                                                </div>
                                                {{-- Switch Info --}}
                                                <div class="min-w-0 flex-1">
                                                    <div class="text-[11px] font-semibold text-gray-800 dark:text-gray-200 truncate" x-text="sw.name || sw.ip"></div>
                                                    <div class="text-[10px] font-mono text-gray-400 dark:text-gray-500" x-text="sw.ip || 'Belum diatur'"></div>
                                                </div>
                                                {{-- Status dot --}}
                                                <span class="flex-shrink-0 w-2 h-2 rounded-full"
                                                      :class="{
                                                          'bg-green-500': sw.status === 'online',
                                                          'bg-red-500': sw.status === 'offline',
                                                          'bg-gray-400': sw.status === 'unknown'
                                                      }"></span>
                                            </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Connection anchor points (4 edges) --}}
                        <div>
                            {{-- Top --}}
                            <div class="absolute -top-2 left-1/2 -translate-x-1/2 w-3.5 h-3.5 rounded-full bg-indigo-500 border-2 border-white dark:border-gray-900 shadow-lg cursor-crosshair z-40 transition-all duration-200"
                                 :class="(connectionMode || isDrawingConnection) ? 'opacity-70 hover:opacity-100 hover:scale-150 pointer-events-auto' : 'opacity-0 pointer-events-none group-hover:pointer-events-auto group-hover:opacity-70 hover:!opacity-100 hover:!scale-150'"
                                 @click.stop="handleAnchorClick(panel.id, 'panel', 'top')"></div>
                            {{-- Bottom --}}
                            <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-3.5 h-3.5 rounded-full bg-indigo-500 border-2 border-white dark:border-gray-900 shadow-lg cursor-crosshair z-40 transition-all duration-200"
                                 :class="(connectionMode || isDrawingConnection) ? 'opacity-70 hover:opacity-100 hover:scale-150 pointer-events-auto' : 'opacity-0 pointer-events-none group-hover:pointer-events-auto group-hover:opacity-70 hover:!opacity-100 hover:!scale-150'"
                                 @click.stop="handleAnchorClick(panel.id, 'panel', 'bottom')"></div>
                            {{-- Left --}}
                            <div class="absolute top-1/2 -left-2 -translate-y-1/2 w-3.5 h-3.5 rounded-full bg-indigo-500 border-2 border-white dark:border-gray-900 shadow-lg cursor-crosshair z-40 transition-all duration-200"
                                 :class="(connectionMode || isDrawingConnection) ? 'opacity-70 hover:opacity-100 hover:scale-150 pointer-events-auto' : 'opacity-0 pointer-events-none group-hover:pointer-events-auto group-hover:opacity-70 hover:!opacity-100 hover:!scale-150'"
                                 @click.stop="handleAnchorClick(panel.id, 'panel', 'left')"></div>
                            {{-- Right --}}
                            <div class="absolute top-1/2 -right-2 -translate-y-1/2 w-3.5 h-3.5 rounded-full bg-indigo-500 border-2 border-white dark:border-gray-900 shadow-lg cursor-crosshair z-40 transition-all duration-200"
                                 :class="(connectionMode || isDrawingConnection) ? 'opacity-70 hover:opacity-100 hover:scale-150 pointer-events-auto' : 'opacity-0 pointer-events-none group-hover:pointer-events-auto group-hover:opacity-70 hover:!opacity-100 hover:!scale-150'"
                                 @click.stop="handleAnchorClick(panel.id, 'panel', 'right')"></div>
                        </div>
                    </div>
                </template>

                {{-- Waypoints for Connections --}}
                <template x-for="conn in connections" :key="'wp-' + conn.id">
                    <template x-if="selectedConnection === conn.id">
                        <div>
                            <template x-for="(wp, index) in (conn.waypoints || [])" :key="index">
                                <div :id="'conn-wp-' + conn.id + '-' + index"
                                     class="absolute w-3.5 h-3.5 bg-blue-500 border-2 border-white dark:border-gray-900 cursor-move z-50 rounded shadow-md hover:scale-125 transition-transform"
                                     :style="`left: ${wp.x}px; top: ${wp.y}px; transform: translate(-50%, -50%)`"
                                     @mousedown.stop="startDragWaypoint($event, conn.id, index)"
                                     @dblclick.stop="deleteWaypoint(conn.id, index)">
                                </div>
                            </template>
                        </div>
                    </template>
                </template>

                {{-- Labels for Connections --}}
                <template x-for="conn in connections" :key="'lbl-' + conn.id">
                    <template x-if="conn.label || conn.cableType && conn.cableType !== 'none'">
                        <div :id="'conn-label-' + conn.id"
                             class="absolute -translate-x-1/2 -translate-y-1/2 px-2 py-0.5 rounded-md bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 pointer-events-auto cursor-pointer hover:border-indigo-500 transition-colors flex items-center gap-1.5"
                             :style="`left: ${getConnectionMidpoint(conn).x}px; top: ${getConnectionMidpoint(conn).y}px; z-index: 20;`"
                             @dblclick.stop="openEditModal(conn.id, 'connection')">
                            
                            <!-- Badges -->
                            <template x-if="conn.cableType === 'FO'">
                                <span class="px-1.5 py-0.5 rounded bg-orange-100 dark:bg-orange-900/40 text-orange-600 dark:text-orange-400 text-[9px] font-bold tracking-wider">FO</span>
                            </template>
                            <template x-if="conn.cableType === 'LAN'">
                                <span class="px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-[9px] font-bold tracking-wider">LAN</span>
                            </template>

                            <template x-if="conn.label">
                                <span class="text-[10px] font-bold text-gray-700 dark:text-gray-300 whitespace-nowrap" x-text="conn.label"></span>
                            </template>
                        </div>
                    </template>
                </template>

            </div>

            {{-- Connection Mode Indicator Overlay --}}
            <div x-show="connectionMode" x-transition
                 class="absolute top-3 left-1/2 -translate-x-1/2 z-50 px-4 py-2 bg-indigo-500 text-white text-xs font-semibold rounded-full shadow-lg shadow-indigo-500/30 flex items-center gap-2">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                </span>
                Klik node pertama, lalu klik node tujuan. Tekan <kbd class="px-1 bg-indigo-600 rounded">Esc</kbd> untuk batal.
            </div>

            {{-- Drop indicator --}}
            <div x-show="isDraggingOver" x-transition
                 class="absolute inset-4 border-2 border-dashed border-indigo-400/50 rounded-2xl pointer-events-none z-40 flex items-center justify-center">
                <div class="bg-indigo-500/10 backdrop-blur-sm rounded-xl px-6 py-3">
                    <p class="text-sm font-semibold text-indigo-500">Lepaskan untuk menempatkan komponen di sini</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- EDIT MODAL --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <template x-teleport="body">
        <div x-show="showEditModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;">
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border border-gray-100 dark:border-gray-800"
                 @click.away="showEditModal = false"
                 x-show="showEditModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                
                <div class="p-5 border-b border-gray-100 dark:border-gray-700/60 flex justify-between items-center">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white" x-text="editModal.title"></h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5" x-text="editModal.nodeType === 'connection' ? 'Label Garis' : 'Nama'"></label>
                        <input type="text" x-model="editModal.name" 
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                               placeholder="Nama komponen atau label...">
                    </div>
                    <template x-if="editModal.nodeType === 'connection'">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Jenis Kabel</label>
                            <select x-model="editModal.cableType"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <option value="none">Tidak Ditentukan</option>
                                <option value="LAN">Kabel LAN (UTP/Ethernet)</option>
                                <option value="FO">Kabel FO (Fiber Optic)</option>
                            </select>
                        </div>
                    </template>
                    <template x-if="['server', 'switch'].includes(editModal.nodeType)">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">IP Address</label>
                            <input type="text" x-model="editModal.ip"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white font-mono focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                   placeholder="192.168.x.x">
                        </div>
                    </template>
                    <template x-if="editModal.nodeType === 'switch'">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Merk / Model</label>
                            <input type="text" x-model="editModal.merk"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                   placeholder="Cisco, Mikrotik, dll...">
                        </div>
                    </template>
                </div>
                
                <div class="p-5 border-t border-gray-100 dark:border-gray-700/60 bg-gray-50 dark:bg-gray-800/50 flex justify-end gap-2">
                    <button @click="showEditModal = false" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">Batal</button>
                    <button @click="saveEditModal()" class="px-4 py-2 bg-indigo-500 text-white rounded-lg text-sm font-semibold hover:bg-indigo-600 shadow-sm shadow-indigo-500/25 transition-colors">Simpan</button>
                </div>
            </div>
        </div>
    </template>

    {{-- CLEAR ALL CONFIRMATION MODAL --}}
    <template x-teleport="body">
        <div x-show="showClearAllModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;">
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border border-gray-100 dark:border-gray-800"
                 @click.away="showClearAllModal = false"
                 x-show="showClearAllModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                
                <div class="p-5 border-b border-gray-100 dark:border-gray-700/60 flex justify-between items-center">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Konfirmasi Hapus Semua</h3>
                    <button @click="showClearAllModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="p-5">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Apakah Anda yakin ingin menghapus semua komponen dan koneksi? Aksi ini tidak bisa dibatalkan.</p>
                </div>
                
                <div class="p-5 border-t border-gray-100 dark:border-gray-700/60 bg-gray-50 dark:bg-gray-800/50 flex justify-end gap-2">
                    <button @click="showClearAllModal = false" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">Batal</button>
                    <button @click="confirmClearAll()" class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm font-semibold hover:bg-red-600 shadow-sm shadow-red-500/25 transition-colors">Ya, Hapus Semua</button>
                </div>
            </div>
        </div>
    </template>

    {{-- Toast Notification --}}
    <template x-teleport="body">
        <div x-show="toast.show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-y-2"
             class="fixed bottom-6 right-6 z-[99999]" style="display: none;">
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl shadow-2xl border"
                 :class="{
                     'bg-green-50 dark:bg-green-900/80 border-green-200 dark:border-green-700 text-green-800 dark:text-green-200': toast.type === 'success',
                     'bg-red-50 dark:bg-red-900/80 border-red-200 dark:border-red-700 text-red-800 dark:text-red-200': toast.type === 'error',
                     'bg-blue-50 dark:bg-blue-900/80 border-blue-200 dark:border-blue-700 text-blue-800 dark:text-blue-200': toast.type === 'info'
                 }">
                <span class="text-sm font-medium" x-text="toast.message"></span>
            </div>
        </div>
    </template>
    </div>
</div>

@push('scripts')
<script>
const initialTopologyData = @json($topologyData);

document.addEventListener('alpine:init', () => {
    Alpine.data('topologyDesigner', () => ({
        // ── State ──
        topologyName: initialTopologyData.name || 'Desain Topologi Baru',
        topologyDate: initialTopologyData.date || new Date().toISOString().split('T')[0],
        zoom: 1,
        panX: 0,
        panY: 0,
        isPanning: false,
        panStartX: 0,
        panStartY: 0,
        hasTopologyData: (initialTopologyData.servers && initialTopologyData.servers.length > 0) || (initialTopologyData.panels && initialTopologyData.panels.length > 0),
        isEditMode: false,
        
        // Nodes
        servers: initialTopologyData.servers || [],
        panels: initialTopologyData.panels || [],
        connections: initialTopologyData.connections || [],
        
        // Drag & Drop
        isDraggingOver: false,
        dragType: null,
        
        // Node dragging
        draggingNode: null,
        draggingNodeType: null,
        dragOffsetX: 0,
        dragOffsetY: 0,
        
        // Selection
        selectedNode: null,
        selectedNodeType: null,
        selectedConnection: null,
        
        // Connections
        connectionMode: false,
        isDrawingConnection: false,
        connectionFrom: null,
        connectionFromType: null,
        drawLineFrom: { x: 0, y: 0 },
        drawLineTo: { x: 0, y: 0 },
        
        // Edit Modal
        showEditModal: false,
        showClearAllModal: false,
        editModal: { title: '', name: '', ip: '', merk: '', status: 'unknown', nodeId: null, nodeType: null, label: '', cableType: 'none' },
        
        // Toast
        toast: { show: false, message: '', type: 'success' },
        
        // Counter for unique IDs
        idCounter: initialTopologyData.idCounter || 10,
        isPinging: false,
        isDirty: false,
        isFullscreen: false,

        // Waypoints dragging state
        isDraggingWaypoint: false,
        dragWaypointConnId: null,
        dragWaypointIndex: null,

        // Undo / Redo
        history: [],
        redoStack: [],
        isRestoringHistory: false,

        // ── Undo / Redo ──
        saveStateForUndo() {
            if (this.isRestoringHistory) return;
            this.history.push({
                servers: JSON.parse(JSON.stringify(this.servers)),
                panels: JSON.parse(JSON.stringify(this.panels)),
                connections: JSON.parse(JSON.stringify(this.connections)),
                idCounter: this.idCounter
            });
            if (this.history.length > 50) this.history.shift();
            this.redoStack = [];
        },

        undo() {
            if (this.history.length === 0) return;
            this.isRestoringHistory = true;
            
            this.redoStack.push({
                servers: JSON.parse(JSON.stringify(this.servers)),
                panels: JSON.parse(JSON.stringify(this.panels)),
                connections: JSON.parse(JSON.stringify(this.connections)),
                idCounter: this.idCounter
            });
            
            const prevState = this.history.pop();
            this.servers = prevState.servers;
            this.panels = prevState.panels;
            this.connections = prevState.connections;
            this.idCounter = prevState.idCounter;
            
            this.selectedNode = null;
            this.selectedConnection = null;
            
            this.$nextTick(() => {
                this.drawConnections();
                this.isRestoringHistory = false;
                this.isDirty = true;
            });
        },
        
        redo() {
            if (this.redoStack.length === 0) return;
            this.isRestoringHistory = true;
            
            this.history.push({
                servers: JSON.parse(JSON.stringify(this.servers)),
                panels: JSON.parse(JSON.stringify(this.panels)),
                connections: JSON.parse(JSON.stringify(this.connections)),
                idCounter: this.idCounter
            });
            
            const nextState = this.redoStack.pop();
            this.servers = nextState.servers;
            this.panels = nextState.panels;
            this.connections = nextState.connections;
            this.idCounter = nextState.idCounter;
            
            this.selectedNode = null;
            this.selectedConnection = null;
            
            this.$nextTick(() => {
                this.drawConnections();
                this.isRestoringHistory = false;
                this.isDirty = true;
            });
        },

        // ── Init ──
        init() {
            // Data is already loaded from initialTopologyData

            document.addEventListener('fullscreenchange', () => {
                this.isFullscreen = !!document.fullscreenElement;
                this.$nextTick(() => { this.drawConnections(); });
            });

            // Pastikan lines tergambar sempurna saat awal masuk halaman
            setTimeout(() => {
                this.drawConnections();
            }, 300);

            // Peringatan sebelum refresh/tutup tab jika ada perubahan yang belum disimpan
            window.addEventListener('beforeunload', (e) => {
                if (this.isDirty) {
                    e.preventDefault();
                    e.returnValue = 'Ada perubahan yang belum disimpan. Yakin ingin keluar?'; 
                }
            });

            // Cegah klik navigasi sidebar jika belum disimpan
            document.body.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (link && this.isDirty) {
                    if (!confirm('Anda memiliki perubahan yang belum disimpan! Yakin ingin meninggalkan halaman?')) {
                        e.preventDefault();
                    }
                }
            });

            // Pantau perubahan agar kita tahu kapan state menjadi "kotor" (belum disave)
            this.$watch('topologyName', () => { this.isDirty = true; });
            this.$watch('topologyDate', () => { this.isDirty = true; });
            
            this.$watch('servers', () => { 
                this.isDirty = true; 
                this.$nextTick(() => this.drawConnections());
            }, { deep: true });
            
            this.$watch('panels', () => { 
                this.isDirty = true; 
                this.$nextTick(() => this.drawConnections());
            }, { deep: true });
            
            this.$watch('connections', () => { 
                this.isDirty = true; 
                this.$nextTick(() => this.drawConnections());
            }, { deep: true });
            
            this.$watch('topologyName', () => { this.isDirty = true; });
            this.$watch('topologyDate', () => { this.isDirty = true; });
        },

        // ── Palette Drag ──
        onPaletteDragStart(event, type) {
            event.dataTransfer.setData('text/plain', type);
            event.dataTransfer.effectAllowed = 'copy';
            this.dragType = type;
        },

        onCanvasDragOver(event) {
            event.dataTransfer.dropEffect = 'copy';
            this.isDraggingOver = true;
        },

        onCanvasDrop(event) {
            if (!this.isEditMode) return;
            this.isDraggingOver = false;
            
            const dragType = event.dataTransfer.getData('text/plain');
            if (!dragType) return;
            
            this.saveStateForUndo();
            if (dragType === 'switch-to-panel') return;

            const wrapper = document.getElementById('canvas-wrapper');
            const rect = wrapper.getBoundingClientRect();
            const x = (event.clientX - rect.left - this.panX) / this.zoom;
            const y = (event.clientY - rect.top - this.panY) / this.zoom;

            if (dragType === 'server') {
                this.addServer(x - 80, y - 30);
            } else if (dragType === 'panel') {
                this.addPanel(x - 100, y - 30);
            } else if (dragType === 'switch') {
                // Switch dropped on canvas (not on panel) — show toast 
                this.showToast('Switch harus di-drop ke dalam Panel!', 'error');
            }
        },

        // ── Add Nodes ──
        addServer(x, y) {
            this.saveStateForUndo();
            const id = 'server-' + (this.idCounter++);
            this.servers.push({
                id, name: 'Server Baru', ip: '', status: 'unknown', x, y
            });
            this.selectNode(id, 'server');
            this.openEditModal(id, 'server');
            this.showToast('Server ditambahkan. Isi detail-nya.', 'info');
        },

        addPanel(x, y) {
            this.saveStateForUndo();
            const id = 'panel-' + (this.idCounter++);
            this.panels.push({
                id, name: 'Panel Baru', x, y, dragOver: false, switches: []
            });
            this.selectNode(id, 'panel');
            this.openEditModal(id, 'panel');
            this.showToast('Panel ditambahkan. Isi detail-nya.', 'info');
        },

        addSwitchToPanel(panelId) {
            this.saveStateForUndo();
            const panel = this.panels.find(p => p.id === panelId);
            if (!panel) return;
            const id = 'sw-' + (this.idCounter++);
            panel.switches.push({
                id, name: 'Switch Baru', ip: '', merk: '', status: 'unknown'
            });
            this.selectNode(id, 'switch');
            this.openEditModal(id, 'switch');
            this.showToast('Switch ditambahkan ke ' + panel.name, 'info');
        },

        // ── Panel Drop Zone for Switches ──
        onSwitchDropToPanel(event, panelId) {
            this.isDraggingOver = false;
            const type = event.dataTransfer.getData('text/plain');
            const panel = this.panels.find(p => p.id === panelId);
            if (panel) panel.dragOver = false;
            
            if (type === 'switch') {
                this.addSwitchToPanel(panelId);
            }
        },

        onPanelDragOver(event, panelId) {
            const panel = this.panels.find(p => p.id === panelId);
            if (panel) panel.dragOver = true;
        },

        onPanelDragLeave(panelId) {
            const panel = this.panels.find(p => p.id === panelId);
            if (panel) panel.dragOver = false;
        },

        // ── Node Dragging ──
        startDragNode(event, nodeId, nodeType) {
            if (!this.isEditMode) return;
            
            this.saveStateForUndo();
            if (this.connectionMode) {
                this.startConnection(event, nodeId, nodeType);
                return;
            }

            // Ignore if clicking on a switch inside panel (switches are not individually draggable on canvas)
            if (nodeType === 'switch') return;

            this.draggingNode = nodeId;
            this.draggingNodeType = nodeType;

            const wrapper = document.getElementById('canvas-wrapper');
            const rect = wrapper.getBoundingClientRect();
            const mouseX = (event.clientX - rect.left - this.panX) / this.zoom;
            const mouseY = (event.clientY - rect.top - this.panY) / this.zoom;

            const node = this.findNode(nodeId, nodeType);
            if (node) {
                this.dragOffsetX = mouseX - node.x;
                this.dragOffsetY = mouseY - node.y;
            }

            event.preventDefault();
        },

        deleteSelectedConnection() {
            if (!this.selectedConnection) return;
            this.saveStateForUndo();
            this.connections = this.connections.filter(c => c.id !== this.selectedConnection);
            this.selectedConnection = null;
        },

        startDragWaypoint(event, connId, index) {
            if (!this.isEditMode) return;
            this.saveStateForUndo();
            this.isDraggingWaypoint = true;
            this.dragWaypointConnId = connId;
            this.dragWaypointIndex = index;
        },

        deleteWaypoint(connId, index) {
            const conn = this.connections.find(c => c.id === connId);
            if (conn && conn.waypoints) {
                this.saveStateForUndo();
                conn.waypoints.splice(index, 1);
            }
        },

        addWaypointToSelected() {
            if (!this.selectedConnection) return;
            const conn = this.connections.find(c => c.id === this.selectedConnection);
            if (!conn) return;
            
            this.saveStateForUndo();
            if (!conn.waypoints) conn.waypoints = [];
            
            // If no waypoints, put it in the exact middle of start and end
            if (conn.waypoints.length === 0) {
                const from = this.getExactAnchorPoint(conn.fromId, conn.fromType, conn.fromAnchor);
                const to = this.getExactAnchorPoint(conn.toId, conn.toType, conn.toAnchor);
                conn.waypoints.push({
                    x: (from.x + to.x) / 2,
                    y: (from.y + to.y) / 2
                });
            } else {
                // Just add it halfway between the last waypoint and the end
                const last = conn.waypoints[conn.waypoints.length - 1];
                const to = this.getExactAnchorPoint(conn.toId, conn.toType, conn.toAnchor);
                conn.waypoints.push({
                    x: (last.x + to.x) / 2,
                    y: (last.y + to.y) / 2
                });
            }
            this.showToast('Waypoint ditambahkan. Silakan geser titik biru.', 'info');
        },

        // ── Canvas Rendering Events ──
        onCanvasMouseDown(event) {
            const targetId = event.target.id;
            const isCanvasClick = targetId === 'canvas-wrapper' || targetId === 'canvas-transform' || targetId === 'connections-canvas' || (event.target.closest('#canvas-wrapper') && !event.target.closest('[class*="absolute group"]'));

            if (isCanvasClick && event.button === 0) {
                // Check if a connection was clicked
                const canvas = document.getElementById('connections-canvas');
                if (canvas) {
                    const ctx = canvas.getContext('2d');
                    const rect = canvas.getBoundingClientRect();
                    const mouseX = event.clientX - rect.left;
                    const mouseY = event.clientY - rect.top;

                    let clickedConnection = null;

                    for (const conn of this.connections) {
                        const from = this.getExactAnchorPoint(conn.fromId, conn.fromType, conn.fromAnchor);
                        const to = this.getExactAnchorPoint(conn.toId, conn.toType, conn.toAnchor);

                        ctx.beginPath();
                        ctx.moveTo(from.x, from.y);

                        if (!conn.waypoints || conn.waypoints.length === 0) {
                            const dx = to.x - from.x;
                            const dy = to.y - from.y;
                            const dist = Math.sqrt(dx * dx + dy * dy);
                            const curvature = Math.max(80, dist * 0.4);
                            const cp1 = this.getBezierControlPoint(from, conn.fromAnchor, curvature);
                            const cp2 = this.getBezierControlPoint(to, conn.toAnchor, curvature);
                            ctx.bezierCurveTo(cp1.x, cp1.y, cp2.x, cp2.y, to.x, to.y);
                        } else {
                            for (const wp of conn.waypoints) {
                                ctx.lineTo(
                                    wp.x * this.zoom + this.panX,
                                    wp.y * this.zoom + this.panY
                                );
                            }
                            ctx.lineTo(to.x, to.y);
                        }

                        // Increase line width for easier hit detection
                        ctx.lineWidth = 30;
                        if (ctx.isPointInStroke(mouseX, mouseY)) {
                            clickedConnection = conn.id;
                            break;
                        }
                    }

                    if (clickedConnection) {
                        this.selectedConnection = clickedConnection;
                        this.selectedNode = null;
                        this.selectedNodeType = null;
                        event.preventDefault();
                        return;
                    }
                }
            }

            // Only pan on middle-click or when clicking empty space
            if (event.button === 1 || (event.button === 0 && isCanvasClick)) {
                this.isPanning = true;
                this.panStartX = event.clientX - this.panX;
                this.panStartY = event.clientY - this.panY;
                event.preventDefault();
            }

            // Deselect on empty space click
            if (isCanvasClick) {
                if (this.isDrawingConnection) {
                    this.cancelConnection();
                }
                if (!this.draggingNode) {
                    this.selectedNode = null;
                    this.selectedNodeType = null;
                    this.selectedConnection = null;
                }
            }
        },

        onCanvasMouseMove(event) {
            // Panning
            if (this.isPanning) {
                this.panX = event.clientX - this.panStartX;
                this.panY = event.clientY - this.panStartY;
            }

            // Node dragging
            if (this.draggingNode) {
                const wrapper = document.getElementById('canvas-wrapper');
                const rect = wrapper.getBoundingClientRect();
                const mouseX = (event.clientX - rect.left - this.panX) / this.zoom;
                const mouseY = (event.clientY - rect.top - this.panY) / this.zoom;

                const node = this.findNode(this.draggingNode, this.draggingNodeType);
                if (node) {
                    node.x = mouseX - this.dragOffsetX;
                    node.y = mouseY - this.dragOffsetY;
                    
                    // Force immediate DOM update so getBoundingClientRect is accurate for anchors!
                    const el = document.getElementById('node-' + this.draggingNode);
                    if (el) {
                        el.style.left = node.x + 'px';
                        el.style.top = node.y + 'px';
                    }
                    
                    // Force immediate redraw to prevent lag
                    this.drawConnections();
                }
            }

            // Waypoint dragging
            if (this.isDraggingWaypoint && this.dragWaypointConnId) {
                const wrapper = document.getElementById('canvas-wrapper');
                const rect = wrapper.getBoundingClientRect();
                const mouseX = (event.clientX - rect.left - this.panX) / this.zoom;
                const mouseY = (event.clientY - rect.top - this.panY) / this.zoom;
                
                const conn = this.connections.find(c => c.id === this.dragWaypointConnId);
                if (conn && conn.waypoints && conn.waypoints[this.dragWaypointIndex]) {
                    conn.waypoints[this.dragWaypointIndex].x = mouseX;
                    conn.waypoints[this.dragWaypointIndex].y = mouseY;
                    
                    // Force immediate DOM update for the waypoint dot
                    const wpEl = document.getElementById('conn-wp-' + conn.id + '-' + this.dragWaypointIndex);
                    if (wpEl) {
                        wpEl.style.left = mouseX + 'px';
                        wpEl.style.top = mouseY + 'px';
                    }

                    this.drawConnections();
                }
            }

            // Drawing connection line
            if (this.isDrawingConnection) {
                const wrapper = document.getElementById('canvas-wrapper');
                const rect = wrapper.getBoundingClientRect();
                this.drawLineTo.x = event.clientX - rect.left;
                this.drawLineTo.y = event.clientY - rect.top;
                
                this.drawConnections();
            }
        },

        onCanvasMouseUp(event) {
            this.isPanning = false;
            
            let didDrag = (this.draggingNode || this.isDraggingWaypoint);
            
            this.draggingNode = null;
            this.draggingNodeType = null;
            this.isDraggingWaypoint = false;
            this.dragWaypointConnId = null;
            this.dragWaypointIndex = null;
            
            if (didDrag) {
                // Posisi diupdate, isDirty otomatis diset true oleh watcher
            }
        },

        onCanvasWheel(event) {
            const delta = event.deltaY > 0 ? -0.08 : 0.08;
            this.zoom = Math.max(0.3, Math.min(2.5, this.zoom + delta));
        },

        // ── Fullscreen ──
        toggleFullscreen() {
            const el = document.getElementById('topology-container');
            if (!document.fullscreenElement) {
                el.requestFullscreen().catch(err => {
                    this.showToast('Browser tidak mendukung fullscreen untuk elemen ini', 'error');
                });
            } else {
                document.exitFullscreen();
            }
        },

        // ── Zoom ──
        zoomIn() { this.zoom = Math.min(2.5, this.zoom + 0.15); },
        zoomOut() { this.zoom = Math.max(0.3, this.zoom - 0.15); },
        resetZoom() { this.zoom = 1; this.panX = 0; this.panY = 0; },

        // ── Selection ──
        selectNode(nodeId, nodeType) {
            if (this.selectedNode === nodeId && this.selectedNodeType === nodeType) {
                // Toggle off if clicking the same node again
                this.selectedNode = null;
                this.selectedNodeType = null;
            } else {
                this.selectedNode = nodeId;
                this.selectedNodeType = nodeType;
                this.selectedConnection = null;
            }
        },

        selectConnection(connId) {
            this.selectedConnection = connId;
            this.selectedNode = null;
            this.selectedNodeType = null;
        },

        // ── Connections ──
        toggleConnectionMode() {
            this.connectionMode = !this.connectionMode;
            if (!this.connectionMode) {
                this.cancelConnection();
            }
        },

        handleAnchorClick(nodeId, nodeType, anchorPos) {
            if (!this.isEditMode) return;
            if (this.isDrawingConnection) {
                this.finishConnection(nodeId, nodeType, anchorPos);
            } else {
                this.startConnection(nodeId, nodeType, anchorPos);
            }
        },

        startConnection(nodeId, nodeType, anchorPos) {
            this.connectionFrom = nodeId;
            this.connectionFromType = nodeType;
            this.connectionFromAnchor = anchorPos;
            this.isDrawingConnection = true;

            const pt = this.getExactAnchorPoint(nodeId, nodeType, anchorPos);
            this.drawLineFrom = { x: pt.x, y: pt.y };
            this.drawLineTo = { x: pt.x, y: pt.y };
        },

        finishConnection(toId, toType, toAnchorPos) {
            if (!this.connectionFrom || this.connectionFrom === toId) {
                this.cancelConnection();
                return;
            }

            // Prevent duplicate
            const exists = this.connections.some(c =>
                (c.fromId === this.connectionFrom && c.toId === toId) ||
                (c.fromId === toId && c.toId === this.connectionFrom)
            );

            if (!exists) {
                this.saveStateForUndo();
                const id = 'conn-' + (this.idCounter++);
                this.connections.push({
                    id,
                    fromId: this.connectionFrom,
                    fromType: this.connectionFromType,
                    fromAnchor: this.connectionFromAnchor,
                    toId: toId,
                    toType: toType,
                    toAnchor: toAnchorPos,
                    style: 'solid',
                    cableType: 'none'
                });
                this.showToast('Koneksi berhasil dibuat!', 'success');
            }

            this.cancelConnection();
        },

        cancelConnection() {
            this.isDrawingConnection = false;
            this.connectionFrom = null;
            this.connectionFromType = null;
            this.connectionFromAnchor = null;
            this.drawConnections();
        },

        // Get bounding box of a node in canvas-wrapper screen coordinates
        getNodeRect(nodeId, nodeType) {
            const wrapper = document.getElementById('canvas-wrapper');
            const el = document.getElementById('node-' + nodeId);
            
            if (wrapper && el) {
                const wRect = wrapper.getBoundingClientRect();
                const eRect = el.getBoundingClientRect();
                return {
                    x: eRect.left - wRect.left,
                    y: eRect.top - wRect.top,
                    w: eRect.width,
                    h: eRect.height
                };
            }
            return { x: 0, y: 0, w: 0, h: 0 };
        },

        // Get exact coordinate of a specific anchor on a node (in Canvas screen coordinates)
        getExactAnchorPoint(nodeId, nodeType, anchorPos) {
            const rect = this.getNodeRect(nodeId, nodeType);
            if (!rect || (rect.w === 0 && rect.h === 0)) return { x: 0, y: 0 };
            
            const x = rect.x;
            const y = rect.y;
            const w = rect.w;
            const h = rect.h;
            
            if (anchorPos === 'top') return { x: x + w/2, y: y };
            if (anchorPos === 'bottom') return { x: x + w/2, y: y + h };
            if (anchorPos === 'left') return { x: x, y: y + h/2 };
            if (anchorPos === 'right') return { x: x + w, y: y + h/2 };
            
            return { x: x + w/2, y: y + h/2 };
        },

        getBezierControlPoint(pt, anchorPos, curvature) {
            let cp = { x: pt.x, y: pt.y };
            if (anchorPos === 'top') cp.y -= curvature;
            else if (anchorPos === 'bottom') cp.y += curvature;
            else if (anchorPos === 'left') cp.x -= curvature;
            else if (anchorPos === 'right') cp.x += curvature;
            // if no anchor, just return point itself
            return cp;
        },

        getConnectionMidpoint(conn) {
            if (conn.waypoints && conn.waypoints.length > 0) {
                const midIndex = Math.floor(conn.waypoints.length / 2);
                return conn.waypoints[midIndex];
            } else {
                const from = this.getExactAnchorPoint(conn.fromId, conn.fromType, conn.fromAnchor);
                const to = this.getExactAnchorPoint(conn.toId, conn.toType, conn.toAnchor);
                
                // Get canvas midpoint
                const midXCanvas = (from.x + to.x) / 2;
                const midYCanvas = (from.y + to.y) / 2;
                
                // Convert back to logical workspace coordinates
                return {
                    x: (midXCanvas - this.panX) / this.zoom,
                    y: (midYCanvas - this.panY) / this.zoom
                };
            }
        },

        getConnectionStatusColor(conn) {
            const fromNode = this.findNode(conn.fromId, conn.fromType);
            const toNode = this.findNode(conn.toId, conn.toType);
            
            const statuses = [];
            
            const addStatus = (node, type) => {
                if (!node) return;
                if (type === 'server') statuses.push(node.status);
                else if (type === 'switch') statuses.push(node.status);
                else if (type === 'panel') {
                    if (node.switches && node.switches.length > 0) {
                        node.switches.forEach(sw => statuses.push(sw.status));
                    }
                }
            };
            
            addStatus(fromNode, conn.fromType);
            addStatus(toNode, conn.toType);
            
            if (statuses.includes('offline')) return '#ef4444'; // Red
            if (statuses.includes('online')) return '#22c55e'; // Green
            return null;
        },

        drawConnections() {
            const canvas = document.getElementById('connections-canvas');
            if (!canvas) return;
            const wrapper = document.getElementById('canvas-wrapper');
            if (!wrapper) return;

            canvas.width = wrapper.clientWidth;
            canvas.height = wrapper.clientHeight;

            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            const isDark = document.documentElement.classList.contains('dark');

            // Draw existing connections as smooth bezier curves
            for (const conn of this.connections) {
                const from = this.getExactAnchorPoint(conn.fromId, conn.fromType, conn.fromAnchor);
                const to = this.getExactAnchorPoint(conn.toId, conn.toType, conn.toAnchor);

                // Draw the line (either bezier or straight through waypoints)
                ctx.beginPath();
                ctx.moveTo(from.x, from.y);

                if (!conn.waypoints || conn.waypoints.length === 0) {
                    // Smooth bezier curve if no waypoints
                    // Calculate distance for curvature
                    const dx = to.x - from.x;
                    const dy = to.y - from.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    const curvature = Math.max(80, dist * 0.4); // Minimum kelenturan

                    const cp1 = this.getBezierControlPoint(from, conn.fromAnchor, curvature);
                    const cp2 = this.getBezierControlPoint(to, conn.toAnchor, curvature);

                    ctx.bezierCurveTo(cp1.x, cp1.y, cp2.x, cp2.y, to.x, to.y);
                } else {
                    // Draw lines through waypoints
                    for (const wp of conn.waypoints) {
                        ctx.lineTo(
                            wp.x * this.zoom + this.panX, 
                            wp.y * this.zoom + this.panY
                        );
                    }
                    ctx.lineTo(to.x, to.y);
                }

                const statusColor = this.getConnectionStatusColor(conn);
                
                if (this.selectedConnection === conn.id) {
                    ctx.strokeStyle = '#6366f1'; // Indigo for selected
                    ctx.lineWidth = 3;
                } else if (statusColor) {
                    ctx.strokeStyle = statusColor; // Red/Green based on status
                    ctx.lineWidth = 2;
                } else {
                    ctx.strokeStyle = isDark ? '#64748b' : '#94a3b8'; // Default Gray
                    ctx.lineWidth = 2;
                }

                ctx.setLineDash(conn.style === 'dashed' ? [6, 4] : []);
                ctx.stroke();

                // Draw small circle at endpoints for visual anchoring
                ctx.setLineDash([]);
                [from, to].forEach(pt => {
                    ctx.beginPath();
                    ctx.arc(pt.x, pt.y, 4, 0, Math.PI * 2);
                    ctx.fillStyle = this.selectedConnection === conn.id ? '#6366f1' : (isDark ? '#64748b' : '#94a3b8');
                    ctx.fill();
                });
                ctx.globalAlpha = 1;

                // Sync label position immediately during drag
                const labelEl = document.getElementById('conn-label-' + conn.id);
                if (labelEl) {
                    const mid = this.getConnectionMidpoint(conn);
                    labelEl.style.left = mid.x + 'px';
                    labelEl.style.top = mid.y + 'px';
                }
            }

            // Draw active connection line (while user is drawing)
            if (this.isDrawingConnection) {
                const from = this.drawLineFrom;
                const to = this.drawLineTo;
                
                const dx = to.x - from.x;
                const dy = to.y - from.y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                const curvature = Math.max(60, dist * 0.4);

                const cp1 = this.getBezierControlPoint(from, this.connectionFromAnchor, curvature);
                // For the target end, we don't know the anchor yet, so just curve toward the source's opposite
                let targetAnchor = 'bottom';
                if (this.connectionFromAnchor === 'top') targetAnchor = 'bottom';
                else if (this.connectionFromAnchor === 'bottom') targetAnchor = 'top';
                else if (this.connectionFromAnchor === 'left') targetAnchor = 'right';
                else if (this.connectionFromAnchor === 'right') targetAnchor = 'left';

                const cp2 = this.getBezierControlPoint(to, targetAnchor, curvature);

                ctx.beginPath();
                ctx.moveTo(from.x, from.y);
                ctx.bezierCurveTo(cp1.x, cp1.y, cp2.x, cp2.y, to.x, to.y);
                ctx.strokeStyle = '#6366f1';
                ctx.lineWidth = 2;
                ctx.setLineDash([6, 4]);
                ctx.globalAlpha = 0.7;
                ctx.stroke();
                ctx.globalAlpha = 1;
                ctx.setLineDash([]);
            }
        },

        // ── Edit Modal ──
        openEditModal(nodeId, nodeType) {
            if (!this.isEditMode) return;
            let node;
            if (nodeType === 'connection') {
                node = this.connections.find(c => c.id === nodeId);
                if (!node) return;
                this.editModal = {
                    title: 'Beri Label Garis',
                    name: node.label || '', 
                    ip: '', merk: '', status: 'unknown',
                    nodeId: nodeId,
                    nodeType: nodeType,
                    label: node.label || '',
                    cableType: node.cableType || 'none'
                };
            } else {
                node = this.findNode(nodeId, nodeType);
                if (!node) return;
                this.editModal = {
                    title: nodeType === 'server' ? 'Edit Server' : (nodeType === 'panel' ? 'Edit Panel' : 'Edit Switch'),
                    name: node.name || '',
                    ip: node.ip || '',
                    merk: node.merk || '',
                    status: node.status || 'unknown',
                    nodeId: nodeId,
                    nodeType: nodeType,
                    label: '',
                    cableType: 'none'
                };
            }
            this.showEditModal = true;
        },

        saveEditModal() {
            this.saveStateForUndo();
            if (this.editModal.nodeType === 'connection') {
                const conn = this.connections.find(c => c.id === this.editModal.nodeId);
                if (!conn) return;
                conn.label = this.editModal.name;
                conn.cableType = this.editModal.cableType;
            } else {
                const node = this.findNode(this.editModal.nodeId, this.editModal.nodeType);
                if (!node) return;

                node.name = this.editModal.name;
                node.ip = this.editModal.ip;
                if (this.editModal.nodeType === 'switch') {
                    node.merk = this.editModal.merk;
                }
                node.status = this.editModal.status;
            }

            this.showEditModal = false;
            this.showToast('Data berhasil diperbarui!', 'success');
        },

        // ── Delete ──
        deleteSelected() {
            if (this.selectedConnection) {
                this.connections = this.connections.filter(c => c.id !== this.selectedConnection);
                this.selectedConnection = null;
                this.showToast('Koneksi dihapus.', 'success');
                return;
            }

            if (!this.selectedNode) return;

            this.saveStateForUndo();

            if (this.selectedNodeType === 'server') {
                this.connections = this.connections.filter(c => c.fromId !== this.selectedNode && c.toId !== this.selectedNode);
                this.servers = this.servers.filter(s => s.id !== this.selectedNode);
            } else if (this.selectedNodeType === 'panel') {
                this.connections = this.connections.filter(c => c.fromId !== this.selectedNode && c.toId !== this.selectedNode);
                this.panels = this.panels.filter(p => p.id !== this.selectedNode);
            } else if (this.selectedNodeType === 'switch') {
                this.connections = this.connections.filter(c => c.fromId !== this.selectedNode && c.toId !== this.selectedNode);
                // Safe way to update nested arrays in Alpine Proxy
                this.panels = this.panels.map(panel => ({
                    ...panel,
                    switches: panel.switches.filter(s => s.id !== this.selectedNode)
                }));
            }

            this.selectedNode = null;
            this.selectedNodeType = null;
            this.showToast('Komponen dihapus.', 'success');
        },

        clearAll() {
            this.showClearAllModal = true;
        },

        confirmClearAll() {
            this.saveStateForUndo();
            this.servers = [];
            this.panels = [];
            this.connections = [];
            this.selectedNode = null;
            this.selectedConnection = null;
            this.showClearAllModal = false;
            this.showToast('Semua komponen dihapus.', 'success');
        },

        // ── Save / Load ──
        async saveTopology(isSilent = false) {
            const data = {
                name: this.topologyName,
                date: this.topologyDate,
                servers: this.servers,
                panels: this.panels,
                connections: this.connections,
                idCounter: this.idCounter
            };
            
            try {
                const response = await fetch('{{ route('topologydesign.save') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });
                
                if (response.ok) {
                    this.isDirty = false; // Reset status dirty
                    if (!isSilent) {
                        this.showToast('Topologi berhasil disimpan ke server!', 'success');
                    }
                } else {
                    if (!isSilent) {
                        this.showToast('Gagal menyimpan topologi.', 'error');
                    }
                }
            } catch (err) {
                console.error(err);
                if (!isSilent) {
                    this.showToast('Gagal terhubung ke server.', 'error');
                }
            }
        },

        // ── Live Ping ──
        async livePing() {
            if (this.isPinging) return;
            
            // Gather all IPs
            let ipsToPing = [];
            
            this.servers.forEach(s => {
                if (s.ip) ipsToPing.push(s.ip);
            });
            
            this.panels.forEach(p => {
                if (p.switches) {
                    p.switches.forEach(sw => {
                        if (sw.ip) ipsToPing.push(sw.ip);
                    });
                }
            });

            if (ipsToPing.length === 0) {
                this.showToast('Tidak ada IP untuk di-ping pada topologi ini.', 'info');
                return;
            }

            this.isPinging = true;

            try {
                const response = await fetch('{{ route('topologydesign.livePing') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ips: ipsToPing })
                });

                if (response.ok) {
                    const statuses = await response.json();
                    
                    // Update servers
                    this.servers.forEach(s => {
                        if (s.ip && statuses[s.ip]) {
                            s.status = statuses[s.ip];
                        }
                    });
                    
                    // Update switches in panels
                    this.panels.forEach(p => {
                        if (p.switches) {
                            p.switches.forEach(sw => {
                                if (sw.ip && statuses[sw.ip]) {
                                    sw.status = statuses[sw.ip];
                                }
                            });
                        }
                    });

                    this.showToast('Live Ping Selesai!', 'success');
                } else if (response.status === 429) {
                    const res = await response.json();
                    this.showToast(res.error || 'Server sedang sibuk, silakan coba lagi.', 'error');
                } else {
                    this.showToast('Gagal melakukan Live Ping.', 'error');
                }
            } catch (err) {
                console.error(err);
                this.showToast('Gagal terhubung ke server.', 'error');
            } finally {
                this.isPinging = false;
            }
        },

        // ── Helpers ──

        findNode(nodeId, nodeType) {
            if (nodeType === 'server') return this.servers.find(s => s.id === nodeId);
            if (nodeType === 'panel') return this.panels.find(p => p.id === nodeId);
            if (nodeType === 'switch') {
                for (let panel of this.panels) {
                    const sw = panel.switches.find(s => s.id === nodeId);
                    if (sw) return sw;
                }
            }
            return null;
        },

        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => { this.toast.show = false; }, 3000);
        }
    }));
});
</script>
@endpush

@endsection
