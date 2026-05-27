<!DOCTYPE html>

<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Toko Barokah Jaya POS' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-link {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-link:hover {
            transform: translateX(4px);
        }

        .card-shadow {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.02);
        }

        .stats-card {
            transition: all 0.2s ease-in-out;
            border: 1px solid #e2e8f0;
        }

        .stats-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.04);
        }

        .modal {
            display: none;
        }

        .modal.active {
            display: flex;
        }

        /* AI CHAT STYLES - Pindahkan dari component */
        #chat-messages {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
            scroll-behavior: smooth;
        }

        #chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        #chat-messages::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        #chat-messages::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
            transition: background 0.3s ease;
        }

        #chat-messages::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }

        .typing-dots .dot-1 {
            animation: typingDot 1.4s infinite;
        }

        .typing-dots .dot-2 {
            animation: typingDot 1.4s infinite 0.2s;
        }

        .typing-dots .dot-3 {
            animation: typingDot 1.4s infinite 0.4s;
        }

        @keyframes typingDot {

            0%,
            60%,
            100% {
                transform: translateY(0);
                opacity: 0.4;
            }

            30% {
                transform: translateY(-5px);
                opacity: 1;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .backdrop-fade-in {
            animation: fadeIn 0.15s ease-out;
        }

        #chat-modal {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #send-icon {
            transition: transform 0.2s ease;
        }

        #chat-icon {
            transition: transform 0.2s ease-in-out;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        #chat-input:focus {
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
        }

        #send-button:hover:not(:disabled) {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .typing-message {
            border-left: 3px solid #3b82f6;
        }

        .typing-text {
            min-height: 1.2em;
        }

        /* TAMBAHKAN STYLE UNTUK MARKDOWN CONTENT */
        .markdown-content h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            color: #1f2937;
        }

        .markdown-content h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-top: 0.875rem;
            margin-bottom: 0.5rem;
            color: #374151;
        }

        .markdown-content h3 {
            font-size: 1.125rem;
            font-weight: 600;
            margin-top: 0.75rem;
            margin-bottom: 0.5rem;
            color: #4b5563;
        }

        .markdown-content strong {
            font-weight: 700;
            color: #111827;
        }

        .markdown-content em {
            font-style: italic;
            color: #4b5563;
        }

        .markdown-content ul {
            list-style-type: disc;
            margin-left: 1.5rem;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .markdown-content ol {
            list-style-type: decimal;
            margin-left: 1.5rem;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .markdown-content li {
            margin-bottom: 0.25rem;
        }

        .markdown-content p {
            margin-bottom: 0.5rem;
        }

        .markdown-content code {
            background-color: #f3f4f6;
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            font-family: 'Courier New', monospace;
            font-size: 0.875em;
            color: #dc2626;
        }

        .markdown-content pre {
            background-color: #1f2937;
            color: #f9fafb;
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .markdown-content pre code {
            background-color: transparent;
            color: inherit;
            padding: 0;
        }

        .markdown-content blockquote {
            border-left: 4px solid #e5e7eb;
            padding-left: 1rem;
            margin-left: 0;
            color: #6b7280;
            font-style: italic;
        }

        .markdown-content hr {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 1rem 0;
        }

        .markdown-content a {
            color: #2563eb;
            text-decoration: underline;
        }

        .markdown-content a:hover {
            color: #1d4ed8;
        }

        /* Style khusus untuk tabel jika AI mengirim tabel */
        .markdown-content table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .markdown-content th,
        .markdown-content td {
            border: 1px solid #e5e7eb;
            padding: 0.5rem;
            text-align: left;
        }

        .markdown-content th {
            background-color: #f9fafb;
            font-weight: 600;
        }
    </style>
    @livewireStyles
</head>

<body class="bg-slate-50 antialiased">
    <div id="app-layout">
        {{ $slot }}
    </div>

    <livewire:ai-chat />

    @livewireScripts
    <script>
        // Toggle Mobile Menu
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
        }

        // Close mobile sidebar when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebar-toggle');

            if (window.innerWidth < 1024) {
                if (!sidebar.contains(event.target) && !toggleBtn?.contains(event.target)) {
                    sidebar.classList.add('-translate-x-full');
                }
            }
        });

        // AI CHAT FUNCTIONS - Pindahkan dari component
        function toggleChat() {
            Livewire.dispatch('toggleChat');
            const icon = document.getElementById('chat-icon');
            if (icon) {
                icon.style.transform = 'rotate(360deg)';
                setTimeout(() => {
                    icon.style.transform = 'rotate(0deg)';
                }, 200);
            }
        }

        function scrollToBottom() {
            const chatMessages = document.getElementById('chat-messages');
            if (chatMessages) {
                setTimeout(() => {
                    chatMessages.scrollTo({
                        top: chatMessages.scrollHeight,
                        behavior: 'smooth'
                    });
                }, 50);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            scrollToBottom();

            Livewire.on('scroll-to-bottom', () => {
                scrollToBottom();
            });

            Livewire.on('start-typing-animation', (event) => {
                const message = event.message;
                simulateTypingAnimation(message);
            });

            const form = document.getElementById('chat-form');
            const sendIcon = document.getElementById('send-icon');

            if (form) {
                form.addEventListener('submit', function(e) {
                    if (sendIcon) {
                        sendIcon.style.transform = 'translateY(-2px)';
                        setTimeout(() => {
                            sendIcon.style.transform = 'translateY(0)';
                        }, 200);
                    }
                });
            }

            document.addEventListener('keydown', function(e) {
                const chatInput = document.getElementById('chat-input');
                if (e.key === 'Enter' && document.activeElement === chatInput && !e.shiftKey) {
                    e.preventDefault();
                    const form = document.getElementById('chat-form');
                    if (form) {
                        form.dispatchEvent(new Event('submit', {
                            cancelable: true,
                            bubbles: true
                        }));
                    }
                }
            });
        });

        function simulateTypingAnimation(message) {
            const messagesContainer = document.getElementById('messages-container');
            const typingIndicator = document.getElementById('typing-indicator');

            if (!messagesContainer) return;

            if (typingIndicator) {
                typingIndicator.remove();
            }

            const messageElement = document.createElement('div');
            messageElement.className = 'flex justify-start message-item';
            messageElement.innerHTML = `
        <div class="max-w-[80%]">
            <div class="flex items-end space-x-2">
                <div class="w-6 h-6 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-robot text-white text-xs"></i>
                </div>
                <div class="bg-white border border-gray-200 rounded-2xl px-4 py-3 shadow-sm typing-message">
                    <div class="text-sm markdown-content typing-text"></div>
                    <p class="text-xs text-gray-500 mt-1 text-right">${new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}</p>
                </div>
            </div>
        </div>
    `;

            messagesContainer.appendChild(messageElement);
            scrollToBottom();

            const typingText = messageElement.querySelector('.typing-text');
            let index = 0;
            const typingSpeed = 10;

            function typeCharacter() {
                if (index < message.length) {
                    const partialMessage = message.substring(0, index + 1);
                    // KONVERSI MARKDOWN KE HTML
                    typingText.innerHTML = marked.parse(partialMessage);
                    index++;
                    scrollToBottom();
                    setTimeout(typeCharacter, typingSpeed);
                } else {
                    // Render final markdown
                    typingText.innerHTML = marked.parse(message);
                    setTimeout(() => {
                        Livewire.dispatch('add-bot-message-from-js', {
                            message: message
                        });
                    }, 500);
                }
            }

            setTimeout(typeCharacter, 500);
        }

        Livewire.hook('morph.updated', ({
            el,
            component
        }) => {
            scrollToBottom();
        });
    </script>
</body>

</html>
