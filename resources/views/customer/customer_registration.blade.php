<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/auth.css', 'resources/css/customer_css/customer_registration.css'])
    
    <style>
        .bg {
            background-image: url("{{ asset('assets/images/car-rental-bg2.jpg') }}");
        }
    </style>

    <title>Customer Registration</title>
</head>

<body>
    <div class="bg"></div>

    <!-- Content on top -->
    <div class="container">
        <div class="registration-form">
            <div class="center-container">
                <img src="{{ asset('assets/logo/VionAuto-logo.png') }}" alt="VionAuto Logo">
                <p>Create your account</p>
            </div>

            <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Personal Information Section -->
                <div class="form-section">
                    <h3 class="section-title">Personal Information</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="middle_name">Middle Name</label>
                            <input type="text" id="middle_name" name="middle_name">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                        <div class="form-group">
                            <label for="birthday">Birthday *</label>
                            <input type="date" id="birthday" name="birthday" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Address *</label>
                        <input type="text" id="address" name="address" placeholder="Street address" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone_number">Phone Number *</label>
                            <input type="tel" id="phone_number" name="phone_number" placeholder="+63 9XX XXX XXXX" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                </div>

                <!-- License Information Section -->
                <div class="form-section">
                    <h3 class="section-title">License Information</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="license_no">License Number *</label>
                            <input type="text" id="license_no" name="license_no" required>
                        </div>
                        <div class="form-group">
                            <label for="license_expiry">License Expiry *</label>
                            <input type="date" id="license_expiry" name="license_expiry" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="valid_id">Valid ID Upload (Image Only - Max 5MB) *</label>
                        <div class="file-input-wrapper">
                            <input type="file" id="valid_id" name="valid_id" accept="image/*" required>
                            <label for="valid_id" class="file-input-label">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                <span>Choose Image</span>
                            </label>
                            <span class="file-name" id="fileName">No file chosen</span>
                        </div>
                        <div id="imagePreview" class="image-preview"></div>
                    </div>
                </div>

                <!-- Account Information Section -->
                <div class="form-section">
                    <h3 class="section-title">Account Information</h3>

                    <div class="form-group">
                        <label for="username">Username *</label>
                        <input type="text" id="username" name="username" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" required>
                        <span class="password-hint">Minimum 8 characters recommended</span>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <input type="password" id="confirm_password" name="password_confirmation" required>
                    </div>

                    <!-- Hidden fields with default values -->
                    <input type="hidden" name="role" value="customer">
                    <input type="hidden" name="status" value="active">
                </div>

                <!-- Terms & Submit -->
                <div class="terms-section">
                    <label class="checkbox-label">
                        <input type="checkbox" name="agree_terms" required>
                        <span>I agree to the Terms and Conditions</span>
                    </label>
                </div>

                <button type="submit">Create Account</button>

                <div class="registration-footer">
                    Already have an account? <a href="/login">Login here</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Handle file input and preview
        const fileInput = document.getElementById('valid_id');
        const fileName = document.getElementById('fileName');
        const imagePreview = document.getElementById('imagePreview');

        fileInput.addEventListener('change', function(e) {
            const file = this.files[0];
            
            if (file) {
                // Check file size (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must not exceed 5MB');
                    this.value = '';
                    fileName.textContent = 'No file chosen';
                    imagePreview.innerHTML = '';
                    return;
                }

                // Check file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file');
                    this.value = '';
                    fileName.textContent = 'No file chosen';
                    imagePreview.innerHTML = '';
                    return;
                }

                // Display file name
                fileName.textContent = file.name;

                // Display image preview
                const reader = new FileReader();
                reader.onload = function(event) {
                    imagePreview.innerHTML = `<img src="${event.target.result}" alt="Valid ID Preview">`;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                fileName.textContent = 'No file chosen';
                imagePreview.innerHTML = '';
            }
        });

        // Password confirmation validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }

            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long!');
                return false;
            }
        });
    </script>
</body>
</html>
