<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magic Pass Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Magic Pass Login
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Enter your email to receive a 4-digit login code
            </p>
        </div>
        
        <div id="email-form" class="mt-8 space-y-6">
            <div>
                <label for="email" class="sr-only">Email address</label>
                <input id="email" name="email" type="email" required 
                       class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                       placeholder="Email address">
            </div>
            <div>
                <button id="send-code-btn" type="button" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Send Login Code
                </button>
            </div>
        </div>

        <div id="code-form" class="mt-8 space-y-6 hidden">
            <div>
                <label for="code" class="sr-only">4-digit code</label>
                <input id="code" name="code" type="text" maxlength="4" required 
                       class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm text-center text-2xl tracking-widest" 
                       placeholder="0000">
            </div>
            <div>
                <button id="verify-code-btn" type="button" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Verify Code
                </button>
            </div>
            <div class="text-center">
                <button id="back-to-email" type="button" 
                        class="text-indigo-600 hover:text-indigo-500">
                    ← Back to email
                </button>
            </div>
        </div>

        <div id="message" class="mt-4 text-center text-sm hidden"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailForm = document.getElementById('email-form');
            const codeForm = document.getElementById('code-form');
            const sendCodeBtn = document.getElementById('send-code-btn');
            const verifyCodeBtn = document.getElementById('verify-code-btn');
            const backToEmailBtn = document.getElementById('back-to-email');
            const messageDiv = document.getElementById('message');
            const emailInput = document.getElementById('email');
            const codeInput = document.getElementById('code');

            function showMessage(text, isError = false) {
                messageDiv.textContent = text;
                messageDiv.className = `mt-4 text-center text-sm ${isError ? 'text-red-600' : 'text-green-600'}`;
                messageDiv.classList.remove('hidden');
                setTimeout(() => messageDiv.classList.add('hidden'), 5000);
            }

            function showCodeForm() {
                emailForm.classList.add('hidden');
                codeForm.classList.remove('hidden');
                codeInput.focus();
            }

            function showEmailForm() {
                codeForm.classList.add('hidden');
                emailForm.classList.remove('hidden');
                emailInput.focus();
            }

            sendCodeBtn.addEventListener('click', async function() {
                const email = emailInput.value.trim();
                if (!email) {
                    showMessage('Please enter your email address', true);
                    return;
                }

                sendCodeBtn.disabled = true;
                sendCodeBtn.textContent = 'Sending...';

                try {
                    const response = await fetch('/magicpass/send', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ email })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        showMessage(data.message);
                        showCodeForm();
                    } else {
                        showMessage(data.message || 'Failed to send code', true);
                    }
                } catch (error) {
                    showMessage('An error occurred. Please try again.', true);
                } finally {
                    sendCodeBtn.disabled = false;
                    sendCodeBtn.textContent = 'Send Login Code';
                }
            });

            verifyCodeBtn.addEventListener('click', async function() {
                const email = emailInput.value.trim();
                const code = codeInput.value.trim();
                
                if (!code || code.length !== 4) {
                    showMessage('Please enter the 4-digit code', true);
                    return;
                }

                verifyCodeBtn.disabled = true;
                verifyCodeBtn.textContent = 'Verifying...';

                try {
                    const response = await fetch('/magicpass/verify', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ email, code })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        showMessage(data.message);
                        // Redirect to dashboard or intended page
                        setTimeout(() => {
                            window.location.href = '/';
                        }, 1000);
                    } else {
                        showMessage(data.message || 'Invalid code', true);
                    }
                } catch (error) {
                    showMessage('An error occurred. Please try again.', true);
                } finally {
                    verifyCodeBtn.disabled = false;
                    verifyCodeBtn.textContent = 'Verify Code';
                }
            });

            backToEmailBtn.addEventListener('click', showEmailForm);

            emailInput.focus();
        });
    </script>
</body>
</html> 