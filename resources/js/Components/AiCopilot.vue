<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import { useNotify } from '@/composables/useNotify';

const { t } = useI18n();
const notify = useNotify();

const props = defineProps({
    isOpen: { type: Boolean, default: false },
    initialPrompt: { type: String, default: '' },
});

const emit = defineEmits(['close']);

const messages = ref([
    { role: 'assistant', content: t('ai_welcome_message', 'Hello! I am Civio AI. Give me your job title or paste your LinkedIn summary, and I will build you a complete professional resume in a few seconds. What is your profession?') }
]);

const input = ref(props.initialPrompt);
const isTyping = ref(false);
const chatContainer = ref(null);
const fileInput = ref(null);
const isUploading = ref(false);

const scrollToBottom = () => {
    nextTick(() => {
        if (chatContainer.value) {
            chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
        }
    });
};

const closeDialog = () => {
    emit('close');
};

const sendMessage = async () => {
    if (!input.value.trim() || isTyping.value) return;

    messages.value.push({ role: 'user', content: input.value });
    const userMsg = input.value;
    input.value = '';
    isTyping.value = true;
    scrollToBottom();

    try {
        const response = await axios.post('/ai/copilot/chat', {
            messages: messages.value
        });
        
        isTyping.value = false;

        if (response.data.action === 'finish') {
            messages.value.push({ role: 'assistant', content: 'Great! I am generating your resume now...' });
            scrollToBottom();
            
            // Redirect to the resume editor or creation endpoint 
            // In a real flow, we either save it directly to DB if logged in, or put it in session and redirect to register
            if (response.data.redirect_url) {
                setTimeout(() => {
                    window.location.href = response.data.redirect_url;
                }, 1500);
            }
        } else {
            messages.value.push({ role: 'assistant', content: response.data.message });
        }
    } catch (e) {
        isTyping.value = false;
        messages.value.push({ role: 'assistant', content: 'I encountered an error. Could you try again?' });
    }
    scrollToBottom();
};

const handleFileUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('photo', file);

    isUploading.value = true;
    try {
        const response = await axios.post('/ai/copilot/upload', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        const url = response.data.url;
        
        messages.value.push({
            role: 'user', 
            content: `[System: User uploaded photo at ${url}]`,
            isPhoto: true,
            photoUrl: url
        });
        
        scrollToBottom();
        
        // Trigger AI response to acknowledge photo
        isTyping.value = true;
        const aiResponse = await axios.post('/ai/copilot/chat', {
            messages: messages.value.map(m => ({ role: m.role, content: m.content }))
        });
        
        isTyping.value = false;

        if (aiResponse.data.action === 'finish') {
            messages.value.push({ role: 'assistant', content: 'Great! I am generating your resume now...' });
            scrollToBottom();
            if (aiResponse.data.redirect_url) {
                setTimeout(() => {
                    window.location.href = aiResponse.data.redirect_url;
                }, 1500);
            }
        } else {
            messages.value.push({ role: 'assistant', content: aiResponse.data.message });
        }
        
    } catch (e) {
        messages.value.push({ role: 'assistant', content: 'There was an error uploading your photo. Please try again or skip it.' });
    } finally {
        isUploading.value = false;
        if (fileInput.value) fileInput.value.value = '';
        scrollToBottom();
    }
};

onMounted(() => {
    if (props.initialPrompt) {
        input.value = props.initialPrompt;
        sendMessage();
    }
});
</script>

<template>
    <div v-if="isOpen" class="w-full flex justify-center animate-in slide-in-from-top-4 duration-500 fade-in">
        <div class="relative bg-slate-900/60 backdrop-blur-xl border-t border-white/5 shadow-inner w-full flex flex-col overflow-hidden" style="height: 50vh; min-height: 400px; max-height: 500px;">
            <!-- Ambient Glow -->
            <div class="absolute top-0 inset-x-0 h-32 bg-gradient-to-b from-indigo-500/10 to-transparent pointer-events-none"></div>
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between bg-slate-900/40 z-10 w-full">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 p-0.5 shadow-lg shadow-indigo-500/20">
                        <div class="w-full h-full bg-slate-900 rounded-[10px] flex items-center justify-center">
                            <span class="text-xl -mt-0.5 ml-0.5">✨</span>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-white font-bold tracking-tight">Civio AI</h3>
                        <p class="text-indigo-400 text-xs font-medium">{{ t('ai_assistant_badge', 'AI Resume Builder') }}</p>
                    </div>
                </div>
                <button @click="closeDialog" class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/10 transition-colors" title="Close AI">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

                <!-- Chat Area -->
                <div ref="chatContainer" class="flex-1 overflow-y-auto p-6 space-y-6 scroll-smooth z-10 custom-scrollbar">
                    <div v-for="(msg, index) in messages" :key="index"
                        class="flex gap-4"
                        :class="msg.role === 'user' ? 'flex-row-reverse' : ''"
                    >
                        <!-- Avatar -->
                        <div v-if="msg.role === 'assistant'" class="w-8 h-8 rounded-full bg-indigo-500 flex-shrink-0 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div v-else class="w-8 h-8 rounded-full bg-slate-700 flex-shrink-0 flex items-center justify-center">
                            👤
                        </div>

                        <!-- Bubble -->
                        <div class="max-w-[80%] px-5 py-3.5 rounded-2xl text-sm leading-relaxed"
                             :class="msg.role === 'user' ? 'bg-indigo-600 text-white rounded-tr-sm' : 'bg-slate-800 border border-white/5 text-slate-300 rounded-tl-sm shadow-xl'">
                            <template v-if="msg.isPhoto">
                                <img :src="msg.photoUrl" class="max-w-[200px] rounded-lg border border-white/10 shadow-md" alt="User upload" />
                            </template>
                            <template v-else>
                                {{ msg.content }}
                            </template>
                        </div>
                    </div>

                    <!-- Typing Indicator -->
                    <div v-if="isTyping" class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div class="bg-slate-800 border border-white/5 px-4 py-3.5 rounded-2xl rounded-tl-sm flex items-center gap-1.5 w-16 shadow-xl">
                            <div class="w-1.5 h-1.5 rounded-full bg-slate-500 animate-bounce"></div>
                            <div class="w-1.5 h-1.5 rounded-full bg-slate-500 animate-bounce" style="animation-delay: 0.2s"></div>
                            <div class="w-1.5 h-1.5 rounded-full bg-slate-500 animate-bounce" style="animation-delay: 0.4s"></div>
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="p-4 bg-slate-900 border-t border-white/10 z-10 w-full relative">
                    <!-- Global Upload Progress/Spinner -->
                    <div v-if="isUploading" class="absolute -top-10 left-1/2 -translate-x-1/2 bg-slate-800 px-4 py-1.5 rounded-full flex items-center gap-2 border border-white/10 shadow-xl z-20">
                        <div class="w-3 h-3 rounded-full border-2 border-indigo-500 border-t-transparent animate-spin"></div>
                        <span class="text-xs text-slate-300 font-medium">Uploading image...</span>
                    </div>

                    <form @submit.prevent="sendMessage" class="relative group">
                        <div class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-2xl blur opacity-20 group-focus-within:opacity-40 transition duration-500"></div>
                        <div class="relative flex items-end gap-2 bg-slate-950/80 rounded-2xl p-2 border border-white/10">
                            <!-- File Upload Button -->
                            <button
                                type="button"
                                @click="fileInput.click()"
                                class="h-10 w-10 shrink-0 rounded-xl hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition-colors mb-0.5"
                                :disabled="isUploading || isTyping"
                                :title="t('upload_photo', 'Upload Photo')"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            </button>
                            <input type="file" ref="fileInput" @change="handleFileUpload" accept="image/*" class="hidden" />

                            <textarea
                                v-model="input"
                                rows="1"
                                placeholder="Type your message..."
                                class="flex-1 overflow-y-auto block w-full resize-none bg-transparent border-0 focus:ring-0 text-white placeholder-slate-500 text-sm py-2 px-3 custom-scrollbar"
                                @keydown.enter.exact.prevent="sendMessage"
                                style="max-height: 120px; min-height: 40px;"
                            ></textarea>
                            <button
                                type="submit"
                                :disabled="!input.trim() || isTyping"
                                class="h-10 w-10 shrink-0 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white flex items-center justify-center transition-colors disabled:opacity-50 disabled:cursor-not-allowed mb-0.5"
                            >
                                <svg class="w-5 h-5 -mt-0.5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V6m0 0l-5 5m5-5l5 5"/></svg>
                            </button>
                        </div>
                    </form>
                    <p class="text-center text-[10px] text-slate-500 mt-3 font-medium">Civio AI can make mistakes. Please verify the generated CV.</p>
                </div>
            </div>
        </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.2);
}
</style>
