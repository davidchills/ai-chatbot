<template>
	<div class="chat-container">

		<div class="chatbox">

			<div class="chat-header">
				<span>💬 AI Chatbot</span>
				<button @click="toggleDarkMode" class="toggle-dark-button">
					{{ isDarkMode ? '☀️ Light' : '🌙 Dark' }}
				</button>				
			</div>

			<div ref="chatbox" class="messages">
				<transition-group name="fade" tag="div">
					<div v-for="(msg, index) in messages" 
						:key="index"
						class="flex"
						:class="msg.type === 'user' ? 'justify-end' : 'justify-start'">
						<div :class="msg.type === 'user' ? 'user-message' : 'bot-message'">
							{{ msg.text }}
						</div>
					</div>

					<div v-if="isTyping" class="flex justify-start">
						<div class="bot-message typing-indicator">
							<span class="dot"></span>
							<span class="dot"></span>
							<span class="dot"></span>
						</div>
					</div>					
				</transition-group>
			</div>

			<div class="input-container">
				<input
				v-model="userInput"
				@keyup.enter="sendMessage"
				class="input-field"
				placeholder="Type your message..."
				/>
				<button @click="sendMessage" class="send-button">Send</button>
			</div>
				
		</div>

	</div>
</template>

<script>
export default {
	data() {
		return {
			userInput: '',
			messages: [],
			isTyping: false,
			isDarkMode: false
		};
	},
	mounted() {
		const storedDarkMode = localStorage.getItem('darkMode');
		if (storedDarkMode !== null) {
			this.isDarkMode = storedDarkMode === 'true';
		}
		else {
			this.isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
		}
		this.applyDarkMode();

		//this.loadChatHistory();

		window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
			if (localStorage.getItem('darkMode') === null) {
				this.isDarkMode = e.matches;
				this.applyDarkMode();
			}
		});			
	},		
	methods: {

		async loadChatHistory() {
			try {
				let response = await fetch('/chat/history', { credentials: 'include' });
				if (response.ok) {
					this.messages = await response.json();
				}
			} 
			catch (error) {
				console.error('Error loading chat history:', error);
			}
		},

		async sendMessage() {
			if (!this.userInput.trim()) { return; }

			this.messages.push({ text: this.userInput, type: 'user' });

			this.isTyping = true;

			try {
				let csrfToken = document.querySelector('meta[name="csrf-token"]');
				let response = await fetch('/chat', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
					},
					body: JSON.stringify({ message: this.userInput }), 
					credentials: 'include'
				});

				let data = await response.json();

				this.isTyping = false;					
				this.messages.push({ text: data.reply, type: 'bot' });

				// Scroll to bottom of chatbox
				this.$nextTick(() => {
					this.$refs.chatbox.scrollTop = this.$refs.chatbox.scrollHeight;
				});

			} 
			catch (error) {
				console.error('Error:', error);
				this.messages.push({ text: '⚠️ Error: Could not connect to the chatbot.', type: 'bot' });
			}

			this.userInput = '';
		},
		toggleDarkMode() {
			this.isDarkMode = !this.isDarkMode;
			localStorage.setItem('darkMode', this.isDarkMode);
			this.applyDarkMode();
		},
		applyDarkMode() {
			document.documentElement.classList.toggle('dark', this.isDarkMode);
		}						
	}
};
</script>