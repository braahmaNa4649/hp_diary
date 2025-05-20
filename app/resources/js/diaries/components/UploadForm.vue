<template>
    <div class="bg-white p-6 rounded-xl shadow-lg w-full relative"
        :class="{ 'pointer-events-none opacity-70': loading }">

        <div v-if="loading"
            class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-20 pointer-events-auto">
            <svg class="animate-spin h-10 w-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <p class="mt-4 text-gray-700 text-lg">アップロード中です...</p>
        </div>

        <form @submit.prevent="handleSubmit" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">一行日記（200文字まで、必須）</label>
                <input type="text" ref="inputText" v-model="text" required :maxlength="maxTextLength"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
            </div>

            <div class="border-2 border-dashed rounded p-6 text-center cursor-pointer transition bg-gray-50 hover:bg-gray-100"
                :class="{ 'bg-blue-50 border-blue-300': isDragOver }" @dragover.prevent="isDragOver = true"
                @dragleave.prevent="isDragOver = false" @drop.prevent="handleDrop" @click="triggerFileInput">
                <p v-if="!image" class="text-gray-500">
                    ここに画像をドラッグ＆ドロップ、またはクリックで選択（2MB以下、jpgのみ)</p>
                <p v-else class="text-gray-700">選択された画像: {{ image.name }}</p>
                <input type="file" ref="fileInput" @change="handleFileChange" accept="image/jpeg" hidden>
            </div>

            <div v-if="previewUrl" class="relative mt-2">
                <img :src="previewUrl" alt="プレビュー" class="w-full max-h-64 object-contain rounded">
                <button type="button" @click="clearImage"
                    class="absolute top-2 right-2 bg-white border rounded-full p-1 shadow hover:bg-red-100">
                    <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div v-if="error" class="text-red-600 text-sm">{{ error }}</div>

            <button type="submit" :disabled="loading"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                送信
            </button>
        </form>
    </div>

    <div class="fixed top-4 right-4 space-y-2 z-50">
        <div v-for="toast in toasts" :key="toast.id" class="px-4 py-2 rounded shadow text-white"
            :class="toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'">
            {{ toast.message }}
        </div>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    name: 'UploadForm',
    data() {
        return {
            text: '',
            maxTextLength: 0,
            maxImageSize: 0,
            image: null,
            previewUrl: '',
            isDragOver: false,
            error: '',
            loading: false,
            toasts: [],
            uploadUrl: '',
            listUrl: '',
            imageType: '',
        }
    },
    mounted() {
        const uploadForm = document.getElementById('upload-form');
        this.maxTextLength = Number(uploadForm.dataset.maxTextLength);
        this.maxImageSize = Number(uploadForm.dataset.maxImageSize);
        this.uploadUrl = uploadForm.dataset.uploadUrl;
        this.listUrl = uploadForm.dataset.listUrl;
        this.imageType = uploadForm.dataset.imageType;
    },
    methods: {
        triggerFileInput() {
            this.$refs.fileInput.click();
        },
        showToast(message, type = 'success') {
            const id = Date.now();
            this.toasts.push({ id, message, type });
            setTimeout(() => {
                const index = this.toasts.findIndex(t => t.id === id);
                if (index !== -1) this.toasts.splice(index, 1);
            }, 3000);
        },
        clearImage() {
            this.image = null;
            this.previewUrl = '';
            this.error = '';
            if (this.$refs.fileInput) this.$refs.fileInput.value = null;
        },
        validateImage(file) {
            this.error = '';

            if (!file) return;

            if (file.type !== 'image/' + this.imageType) {
                this.error = this.imageType + '形式の画像のみアップロードできます';
                this.clearImage();
                return;
            }

            if (file.size > this.maxImageSize) {
                this.error = '画像サイズは2MB以下にしてください';
                this.clearImage();
                return;
            }

            this.image = file;
            this.previewUrl = URL.createObjectURL(file);
        },
        getByteSize(str) {
            return new TextEncoder().encode(str).length;
        },
        handleFileChange(e) {
            this.validateImage(e.target.files[0]);
        },
        handleDrop(e) {
            this.isDragOver = false;
            this.validateImage(e.dataTransfer.files[0]);
        },
        async handleSubmit() {
            const zenkakuLength = this.maxTextLength / 2;
            const hankakuLength = this.maxTextLength;
            const message = 'テキストは半角' + hankakuLength + '文字、全角' + zenkakuLength + '文字までです'
            if (this.getByteSize(this.text.length) > this.maxTextLength) {
                this.error = this.error || message;
                return;
            }

            const formData = new FormData();
            formData.append('text', this.text);
            if (this.image) {
                formData.append('image', this.image);
            }

            this.loading = true;
            try {
                await axios.post(this.uploadUrl, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                });
                this.showToast('アップロード成功', 'success');
                this.text = '';
                this.clearImage();
                setTimeout(() => {
                    window.location.href = this.listUrl;
                }, 1000);
            } catch (err) {
                const message = err.response?.data?.message || 'アップロードに失敗しました';
                this.showToast(message, 'error');
                this.error = message;
                this.loading = false;
            } finally {
            }
        }
    }
}
</script>
