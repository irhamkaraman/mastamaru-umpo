<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="coordinatePicker({
            name_x: $wire.$entangle('data.name_x'),
            name_y: $wire.$entangle('data.name_y'),
            nim_x: $wire.$entangle('data.nim_x'),
            nim_y: $wire.$entangle('data.nim_y'),
            number_x: $wire.$entangle('data.number_x'),
            number_y: $wire.$entangle('data.number_y'),
            faculty_x: $wire.$entangle('data.faculty_x'),
            faculty_y: $wire.$entangle('data.faculty_y'),
            bg_image: $wire.$entangle('data.background_image'),
            text_color: $wire.$entangle('data.text_color'),
            name_size: $wire.$entangle('data.font_size_name'),
            nim_size: $wire.$entangle('data.font_size_nim'),
            number_size: $wire.$entangle('data.font_size_number')
        })"
        class="relative border border-gray-300 dark:border-gray-700 rounded-xl overflow-hidden bg-gray-50 dark:bg-gray-900 flex flex-col items-center justify-center p-4"
        style="min-height: 400px;"
    >
        <template x-if="imageUrl">
            <div class="relative inline-block w-full overflow-auto shadow-inner bg-gray-200 dark:bg-gray-800 rounded-lg p-2" style="max-height: 800px;">
                <div class="relative origin-top-left" x-ref="container" :style="`width: ${imageWidth}px; height: ${imageHeight}px; transform: scale(${scale});`">
                    <!-- Base Image -->
                    <img :src="imageUrl" x-ref="image" @load="initImage()" class="absolute inset-0 max-w-none pointer-events-none shadow-sm rounded" />
                    
                    <!-- Draggable Points -->
                    <template x-if="imageLoaded">
                        <div>
                            <!-- Nama -->
                            <div class="absolute cursor-move border-2 border-red-500 bg-red-500 bg-opacity-20 flex flex-col items-start px-2 py-1 select-none"
                                 :style="`left: ${name_x}px; top: ${name_y}px; color: ${text_color}; font-size: ${name_size}px; line-height: 1; min-width: 200px;`"
                                 @mousedown="startDrag($event, 'name')">
                                <span class="bg-red-500 text-white text-xs px-1 rounded-sm absolute -top-5 left-0">Nama</span>
                                NAMA PESERTA
                            </div>

                            <!-- NIM -->
                            <div class="absolute cursor-move border-2 border-blue-500 bg-blue-500 bg-opacity-20 flex flex-col items-start px-2 py-1 select-none"
                                 :style="`left: ${nim_x}px; top: ${nim_y}px; color: ${text_color}; font-size: ${nim_size}px; line-height: 1; min-width: 150px;`"
                                 @mousedown="startDrag($event, 'nim')">
                                <span class="bg-blue-500 text-white text-xs px-1 rounded-sm absolute -top-5 left-0">NIM</span>
                                21000000
                            </div>

                            <!-- Nomor Sertifikat -->
                            <div class="absolute cursor-move border-2 border-green-500 bg-green-500 bg-opacity-20 flex flex-col items-start px-2 py-1 select-none"
                                 :style="`left: ${number_x}px; top: ${number_y}px; color: ${text_color}; font-size: ${number_size}px; line-height: 1; min-width: 250px;`"
                                 @mousedown="startDrag($event, 'number')">
                                <span class="bg-green-500 text-white text-xs px-1 rounded-sm absolute -top-5 left-0">Nomor</span>
                                001/PAN/MASTAMARU/...
                            </div>

                            <!-- Fakultas -->
                            <div class="absolute cursor-move border-2 border-purple-500 bg-purple-500 bg-opacity-20 flex flex-col items-start px-2 py-1 select-none"
                                 :style="`left: ${faculty_x || 0}px; top: ${faculty_y || 0}px; color: ${text_color}; font-size: ${nim_size}px; line-height: 1; min-width: 200px;`"
                                 @mousedown="startDrag($event, 'faculty')">
                                <span class="bg-purple-500 text-white text-xs px-1 rounded-sm absolute -top-5 left-0">Fakultas</span>
                                FAKULTAS TEKNIK
                            </div>
                        </div>
                    </template>
                </div>
                
                <div class="mt-4 flex gap-2 justify-center w-full sticky bottom-0 left-0 bg-white dark:bg-gray-800 p-2 border-t dark:border-gray-700">
                    <button type="button" @click.prevent="zoomIn()" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded text-sm hover:bg-gray-300 transition shadow">Zoom In</button>
                    <button type="button" @click.prevent="zoomOut()" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded text-sm hover:bg-gray-300 transition shadow">Zoom Out</button>
                    <button type="button" @click.prevent="resetZoom()" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded text-sm hover:bg-gray-300 transition shadow">Reset Zoom</button>
                </div>
            </div>
        </template>

        <template x-if="!imageUrl">
            <div class="text-gray-500 text-center flex flex-col items-center">
                <svg class="w-12 h-12 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <p>Silakan upload Background Image, lalu klik <b>Save Changes</b> (Simpan) terlebih dahulu.</p>
                <p class="text-sm mt-1">Visual Editor interaktif akan muncul setelah gambar tersimpan.</p>
            </div>
        </template>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            if (!window.Alpine.data('coordinatePicker')) {
                Alpine.data('coordinatePicker', (params) => ({
                    name_x: params.name_x,
                    name_y: params.name_y,
                    nim_x: params.nim_x,
                    nim_y: params.nim_y,
                    number_x: params.number_x,
                    number_y: params.number_y,
                    faculty_x: params.faculty_x,
                    faculty_y: params.faculty_y,
                    
                    text_color: params.text_color,
                    name_size: params.name_size,
                    nim_size: params.nim_size,
                    number_size: params.number_size,
                    
                    bg_image: params.bg_image,
                    
                    imageLoaded: false,
                    imageWidth: 0,
                    imageHeight: 0,
                    scale: 1,
                    
                    draggingEl: null,
                    startX: 0,
                    startY: 0,
                    startLeft: 0,
                    startTop: 0,

                    get imageUrl() {
                        if (typeof this.bg_image === 'string' && this.bg_image !== '') {
                            return '/storage/' + this.bg_image;
                        }
                        return null;
                    },

                    initImage() {
                        this.imageWidth = this.$refs.image.naturalWidth;
                        this.imageHeight = this.$refs.image.naturalHeight;
                        this.imageLoaded = true;
                        
                        const containerWidth = this.$el.parentElement.clientWidth - 40;
                        if (this.imageWidth > containerWidth) {
                            this.scale = containerWidth / this.imageWidth;
                        }
                    },
                    
                    zoomIn() {
                        this.scale = Math.min(this.scale + 0.1, 2);
                    },
                    
                    zoomOut() {
                        this.scale = Math.max(this.scale - 0.1, 0.2);
                    },
                    
                    resetZoom() {
                        this.scale = 1;
                    },

                    startDrag(e, type) {
                        e.preventDefault();
                        this.draggingEl = type;
                        this.startX = e.clientX;
                        this.startY = e.clientY;
                        
                        this.startLeft = parseInt(this[type + '_x'] || 0);
                        this.startTop = parseInt(this[type + '_y'] || 0);

                        const moveHandler = (moveEvent) => this.onDrag(moveEvent);
                        const upHandler = () => {
                            document.removeEventListener('mousemove', moveHandler);
                            document.removeEventListener('mouseup', upHandler);
                            this.draggingEl = null;
                        };

                        document.addEventListener('mousemove', moveHandler);
                        document.addEventListener('mouseup', upHandler);
                    },

                    onDrag(e) {
                        if (!this.draggingEl) return;
                        
                        const dx = (e.clientX - this.startX) / this.scale;
                        const dy = (e.clientY - this.startY) / this.scale;
                        
                        let newX = Math.round(this.startLeft + dx);
                        let newY = Math.round(this.startTop + dy);
                        
                        newX = Math.max(0, Math.min(newX, this.imageWidth));
                        newY = Math.max(0, Math.min(newY, this.imageHeight));
                        
                        this[this.draggingEl + '_x'] = newX;
                        this[this.draggingEl + '_y'] = newY;
                    }
                }));
            }
        });
    </script>
</x-dynamic-component>
