class ChatWidget {
    constructor() {
        this.isWaitingForResponse = false;
        this.rateLimitBackoff = 0;
        this.initialize();
    }

    initialize() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.init());
        } else {
            this.init();
        }
    }

    init() {
        this.container = document.querySelector('.chat-widget');
        if (!this.container) {
            console.error('Chat widget container not found');
            return;
        }
        this.initializeChat();
        this.bindEvents();
    }

    initializeChat() {
        const chatHTML = `
            <div class="chat-widget">
                <div class="chat-button">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="chat-window">
                    <div class="chat-header">
                        <span>Agriculture Assistant</span>
                        <span class="chat-close">&times;</span>
                    </div>
                    <div class="chat-messages">
                        <div class="message bot">
                            Hello! I'm your agriculture assistant. How can I help you today?
                        </div>
                        <div class="typing-indicator">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    <div class="chat-input">
                        <input type="text" placeholder="Type your message...">
                        <button>Send</button>
                    </div>
                </div>
            </div>
        `;
        this.container.innerHTML = chatHTML;
    }

    bindEvents() {
        const chatButton = this.container.querySelector('.chat-button');
        const chatWindow = this.container.querySelector('.chat-window');
        const chatClose = this.container.querySelector('.chat-close');
        const chatInput = this.container.querySelector('.chat-input input');
        const sendButton = this.container.querySelector('.chat-input button');

        if (!chatButton || !chatWindow || !chatClose || !chatInput || !sendButton) {
            console.error('Chat widget elements not found');
            return;
        }

        chatButton.addEventListener('click', () => {
            chatWindow.classList.add('active');
            chatButton.style.display = 'none';
            chatInput.focus();
        });

        chatClose.addEventListener('click', () => {
            chatWindow.classList.remove('active');
            chatButton.style.display = 'flex';
        });

        const sendMessage = async () => {
            const message = chatInput.value.trim();
            if (message && !this.isWaitingForResponse) {
                this.addMessage(message, 'user');
                chatInput.value = '';
                chatInput.focus();
                
                if (this.rateLimitBackoff > 0) {
                    const waitTime = Math.ceil(this.rateLimitBackoff / 60000);
                    this.addMessage(`Please wait ${waitTime} minute(s) before sending another message due to rate limiting.`, 'bot');
                    return;
                }

                this.showTypingIndicator();
                this.isWaitingForResponse = true;
                await this.sendToServer(message);
                this.isWaitingForResponse = false;
            }
        };

        sendButton.addEventListener('click', sendMessage);
        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }

    addMessage(message, type) {
        const messagesContainer = this.container.querySelector('.chat-messages');
        const messageElement = document.createElement('div');
        messageElement.classList.add('message', type);
        messageElement.textContent = message;
        messagesContainer.appendChild(messageElement);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    showTypingIndicator() {
        const indicator = this.container.querySelector('.typing-indicator');
        if (indicator) {
            indicator.classList.add('active');
        }
    }

    hideTypingIndicator() {
        const indicator = this.container.querySelector('.typing-indicator');
        if (indicator) {
            indicator.classList.remove('active');
        }
    }

    async sendToServer(message) {
        try {
            const response = await fetch('/agriculture-portal/includes/chat_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();
            this.hideTypingIndicator();

            if (data.error) {
                if (data.error.includes('rate limit')) {
                    this.rateLimitBackoff = Date.now() + 3600000; // 1 hour backoff
                    this.addMessage('The service is currently experiencing high demand. Please try again in about an hour.', 'bot');
                } else {
                    this.addMessage('Sorry, there was an error: ' + data.error, 'bot');
                }
            } else if (data.response) {
                this.rateLimitBackoff = 0;
                this.addMessage(data.response, 'bot');
            }
        } catch (error) {
            this.hideTypingIndicator();
            this.addMessage('Sorry, there was an error connecting to the server. Please try again later.', 'bot');
            console.error('Chat error:', error);
        }
    }
}

// Initialize when the script loads
new ChatWidget();
