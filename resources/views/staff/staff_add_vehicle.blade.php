@extends('layouts.staff_layout')

@section('styles')
   @vite(['resources/css/staff_css/staff_add_vehicle.css'])
@endsection

@section('content')
<div class="cars-page">
    <div class="page-header">
        <h1 class="page-title">Add New Vehicle</h1>
    </div>
    <button type="button" class="back-btn" onclick="history.back()">
        <span class="back-btn-icon"><img src="{{ asset('assets/icons/Back.png') }}" alt="Back"></span>
        <span>Back</span>
    </button>

    <div class="add-form-container">
        <h2 class="form-title">Vehicle Details</h2>

        @if ($errors->any())
            <div style="background: #fee; border: 1px solid #fcc; padding: 12px; border-radius: 8px; margin-bottom: 20px; color: #c33;">
                <strong>Validation Errors:</strong>
                <ul style="margin: 8px 0 0 20px; padding: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div style="background: #fee; border: 1px solid #fcc; padding: 12px; border-radius: 8px; margin-bottom: 20px; color: #c33;">
                <strong>Error:</strong> {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('staff.vehicles.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Image Upload Section --}}
            <div class="image-section">
                <label>Vehicle Photos (1-3 images) *</label>
                <div class="image-preview-wrap">
                    <div class="images-grid" id="imagesGrid">
                        <!-- Image Slot 1 -->
                        <div class="image-slot">
                            <div class="image-preview" id="imagePreview1">
                                <span class="no-img">Image 1</span>
                            </div>
                            <input type="file" id="imageInput1" name="images[]" class="image-input" accept="image/*">
                            <label for="imageInput1" class="file-input-label">Choose Image 1</label>
                        </div>

                        <!-- Image Slot 2 -->
                        <div class="image-slot">
                            <div class="image-preview" id="imagePreview2">
                                <span class="no-img">Image 2</span>
                            </div>
                            <input type="file" id="imageInput2" name="images[]" class="image-input" accept="image/*">
                            <label for="imageInput2" class="file-input-label">Choose Image 2</label>
                        </div>

                        <!-- Image Slot 3 -->
                        <div class="image-slot">
                            <div class="image-preview" id="imagePreview3">
                                <span class="no-img">Image 3</span>
                            </div>
                            <input type="file" id="imageInput3" name="images[]" class="image-input" accept="image/*">
                            <label for="imageInput3" class="file-input-label">Choose Image 3</label>
                        </div>
                    </div>
                    <small style="color: #666; display: block; margin-top: 10px;">Select 1 to 3 images (max 2MB each). At least 1 image is required.</small>
                    @error('images') <span class="error">{{ $message }}</span> @enderror
                    @error('images.*') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="brand">Brand *</label>
                    <input type="text" id="brand" name="brand" required value="{{ old('brand') }}">
                    @error('brand') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="model">Model *</label>
                    <input type="text" id="model" name="model" required value="{{ old('model') }}">
                    @error('model') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="color">Color</label>
                    <input type="text" id="color" name="color" value="{{ old('color') }}">
                    @error('color') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="plate_no">Plate No. *</label>
                    <input type="text" id="plate_no" name="plate_no" required value="{{ old('plate_no') }}">
                    @error('plate_no') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="sedan" {{ old('category') == 'sedan' ? 'selected' : '' }}>Sedan</option>
                        <option value="suv" {{ old('category') == 'suv' ? 'selected' : '' }}>SUV</option>
                        <option value="van" {{ old('category') == 'van' ? 'selected' : '' }}>Van</option>
                        <option value="pickup" {{ old('category') == 'pickup' ? 'selected' : '' }}>Pickup Truck</option>
                    </select>
                    @error('category') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="daily_rate">Daily Rate (₱) *</label>
                    <input type="number" id="daily_rate" name="daily_rate" step="0.01" required value="{{ old('daily_rate') }}">
                    @error('daily_rate') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group" style="margin-top: 16px;">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; resize:vertical; font-family:inherit;">{{ old('description') }}</textarea>
                @error('description') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('staff.vehicles') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Add Vehicle</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInputs = [
            { input: document.getElementById('imageInput1'), preview: document.getElementById('imagePreview1') },
            { input: document.getElementById('imageInput2'), preview: document.getElementById('imagePreview2') },
            { input: document.getElementById('imageInput3'), preview: document.getElementById('imagePreview3') },
        ];

        // Handle file selection for each image slot
        imageInputs.forEach((element, index) => {
            element.input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                
                if (file) {
                    // Validate file type
                    if (!file.type.startsWith('image/')) {
                        alert('Please select a valid image file');
                        element.input.value = '';
                        element.preview.innerHTML = `<span class="no-img">Image ${index + 1}</span>`;
                        return;
                    }

                    // Validate file size (2MB)
                    if (file.size > 2048 * 1024) {
                        alert('File size must be less than 2MB');
                        element.input.value = '';
                        element.preview.innerHTML = `<span class="no-img">Image ${index + 1}</span>`;
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const img = document.createElement('img');
                        img.src = event.target.result;
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'cover';
                        img.style.borderRadius = '4px';
                        element.preview.innerHTML = '';
                        element.preview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                } else {
                    element.preview.innerHTML = `<span class="no-img">Image ${index + 1}</span>`;
                }
            });
        });

        // Validate form before submission
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            // Check if at least 1 image is selected
            let hasImage = false;
            imageInputs.forEach(element => {
                if (element.input.files && element.input.files[0]) {
                    hasImage = true;
                }
            });

            if (!hasImage) {
                e.preventDefault();
                alert('Please select at least 1 image');
                return false;
            }
        });
    });
</script>
@endsection
