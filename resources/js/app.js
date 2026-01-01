import './bootstrap';
import Alpine from 'alpinejs';
import Cropper from 'cropperjs';
import axios from 'axios';

window.Alpine = Alpine;
window.Cropper = Cropper;

Alpine.data('avatarUploader', (initialAvatarUrl) => ({
    avatarUrl: initialAvatarUrl,
    isCropping: false,
    isUploading: false,
    progress: 0,
    uploadStatus: '', // 'preparing', 'uploading', 'saving'
    errorMessage: '',
    cropper: null,
    zoomLevel: 1,
    
    init() {
        // Any init logic
    },

    onFileSelect(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validation
        const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            this.showError('Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.');
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            // We will compress it later, but for initial select we can warn or just accept
            // The requirement says: "Ukuran maksimal file 2MB (lakukan kompresi otomatis tanpa notifikasi ke user jika melebihi)"
            // So we accept it and handle it in crop/compress stage.
            // However, loading a huge image into Cropper might crash browser.
            // Let's set a hard limit of 10MB for safety.
            if (file.size > 10 * 1024 * 1024) {
                this.showError('File terlalu besar (Max 10MB).');
                return;
            }
        }

        this.errorMessage = '';
        this.startCropper(file);
        
        // Reset input so same file can be selected again if cancelled
        event.target.value = ''; 
    },

    startCropper(file) {
        this.isCropping = true;
        
        this.$nextTick(() => {
            const imageElement = document.getElementById('crop-image');
            if (this.cropper) {
                this.cropper.destroy();
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                imageElement.src = e.target.result;
                this.cropper = new Cropper(imageElement, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    responsive: true,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    zoom: (e) => {
                        this.zoomLevel = e.detail.ratio;
                    },
                    ready: () => {
                        this.zoomLevel = 1; // Reset zoom level display on ready
                    }
                });
            };
            reader.readAsDataURL(file);
        });
    },

    rotate(degree) {
        if (this.cropper) this.cropper.rotate(degree);
    },

    zoom(ratio) {
        if (this.cropper) this.cropper.zoom(ratio);
    },
    
    setZoom(value) {
        if (this.cropper) {
            this.cropper.zoomTo(parseFloat(value));
        }
    },
    
    resetCrop() {
        if (this.cropper) this.cropper.reset();
    },

    cancelCrop() {
        this.isCropping = false;
        if (this.cropper) {
            this.cropper.destroy();
            this.cropper = null;
        }
    },

    saveCrop() {
        if (!this.cropper) return;

        this.isCropping = false;
        this.isUploading = true;
        this.progress = 0;
        this.uploadStatus = 'Mempersiapkan...';
        this.errorMessage = '';

        // Get cropped canvas
        const canvas = this.cropper.getCroppedCanvas({
            width: 800,
            height: 800,
            minWidth: 200,
            minHeight: 200,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        if (!canvas) {
            this.showError('Gagal memproses gambar.');
            this.isUploading = false;
            return;
        }

        // Convert to Blob (WEBP, 0.8 quality)
        canvas.toBlob((blob) => {
            if (!blob) {
                this.showError('Gagal membuat file gambar.');
                this.isUploading = false;
                return;
            }

            this.uploadFile(blob);

        }, 'image/webp', 0.8);
    },

    uploadFile(blob) {
        const formData = new FormData();
        formData.append('avatar', blob, 'avatar.webp');

        this.uploadStatus = 'Mengupload...';

        axios.post('/profile/avatar', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            onUploadProgress: (progressEvent) => {
                const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                this.progress = percentCompleted;
                if (percentCompleted === 100) {
                    this.uploadStatus = 'Menyimpan...';
                }
            }
        })
        .then(response => {
            this.isUploading = false;
            this.avatarUrl = response.data.url;
            // Show success notification (can use global notification system if available)
            alert('Avatar berhasil diperbarui!');
            
            // Clean up
            if (this.cropper) {
                this.cropper.destroy();
                this.cropper = null;
            }
        })
        .catch(error => {
            this.isUploading = false;
            console.error(error);
            let msg = 'Terjadi kesalahan saat upload.';
            if (error.response && error.response.data && error.response.data.message) {
                msg = error.response.data.message;
            }
            this.showError(msg);
        });
    },

    showError(message) {
        this.errorMessage = message;
        // Auto hide error after 5 seconds
        setTimeout(() => {
            this.errorMessage = '';
        }, 5000);
    }
}));

Alpine.data('dropdownInput', (optionsData, initialValue = '') => ({
    options: optionsData || [],
    selectedValue: '',
    customValue: '',
    isCustom: false,

    init() {
        if (initialValue) {
            if (this.options.includes(initialValue)) {
                this.selectedValue = initialValue;
            } else {
                this.selectedValue = 'custom';
                this.customValue = initialValue;
                this.isCustom = true;
            }
        }

        this.$watch('selectedValue', (value) => {
            if (value === 'custom') {
                this.isCustom = true;
                this.customValue = '';
            } else {
                this.isCustom = false;
            }
        });
    },

    get finalValue() {
        return this.isCustom ? this.customValue : this.selectedValue;
    },

    reset() {
        this.isCustom = false;
        this.selectedValue = '';
        this.customValue = '';
    }
}));

Alpine.start();
