<?php
session_start();
if (!isset($_SESSION['farmer_login_user'])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plant Disease Detection - Smart Agriculture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        :root {
            --primary-color: #2ecc71;
            --secondary-color: #27ae60;
            --accent-color: #3498db;
            --background-color: #f8f9fa;
            --text-color: #2c3e50;
            --border-radius: 15px;
            --transition-speed: 0.3s;
        }

        body {
            background: var(--background-color);
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
        }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
            animation: slideDown 0.6s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .page-header h2 {
            color: var(--text-color);
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 2.5rem;
        }

        .page-header p {
            color: #666;
            font-size: 1.1em;
            max-width: 600px;
            margin: 0 auto;
        }

        .upload-container {
            background: white;
            border-radius: var(--border-radius);
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: transform var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .upload-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }

        .drop-zone {
            height: 300px;
            padding: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            cursor: pointer;
            color: #444;
            border: 3px dashed var(--primary-color);
            border-radius: var(--border-radius);
            background: #f8f9fa;
            transition: all var(--transition-speed) ease;
            position: relative;
            overflow: hidden;
        }

        .drop-zone:hover {
            background: #e3f2fd;
            border-color: var(--accent-color);
            transform: scale(1.01);
        }

        .drop-zone--over {
            border-style: solid;
            background: #e3f2fd;
            border-color: var(--accent-color);
            transform: scale(1.02);
        }

        .drop-zone__prompt {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            transition: all var(--transition-speed) ease;
        }

        .drop-zone__prompt i {
            font-size: 3em;
            color: var(--primary-color);
            margin-bottom: 10px;
            transition: transform var(--transition-speed) ease;
        }

        .drop-zone:hover .drop-zone__prompt i {
            transform: translateY(-5px);
        }

        .drop-zone__thumb {
            width: 100%;
            height: 100%;
            border-radius: var(--border-radius);
            overflow: hidden;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
            opacity: 0;
            transition: opacity var(--transition-speed) ease;
        }

        .drop-zone__thumb[style*="background-image"] {
            opacity: 1;
        }

        .loading {
            display: none;
            text-align: center;
            margin: 20px 0;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .result-box {
            display: none;
            margin-top: 30px;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .info-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .info-card h5 {
            color: var(--text-color);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-card ol {
            padding-left: 20px;
        }

        .info-card li {
            margin-bottom: 12px;
            position: relative;
            padding-left: 5px;
            transition: transform var(--transition-speed) ease;
        }

        .info-card li:hover {
            transform: translateX(5px);
        }

        .alert {
            border: none;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-top: 20px;
            transition: all var(--transition-speed) ease;
        }

        .alert-info {
            background-color: rgba(52, 152, 219, 0.1);
            border-left: 4px solid var(--accent-color);
        }

        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            border-left: 4px solid var(--primary-color);
        }

        .alert-warning {
            background-color: rgba(241, 196, 15, 0.1);
            border-left: 4px solid #f1c40f;
        }

        .alert-danger {
            background-color: rgba(231, 76, 60, 0.1);
            border-left: 4px solid #e74c3c;
        }

        .confidence-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 12px;
            background: rgba(46, 204, 113, 0.1);
            border-radius: 20px;
            font-size: 0.9em;
            color: var(--primary-color);
            margin-top: 10px;
        }

        /* Accordion Styling */
        .accordion {
            margin-top: 20px;
        }

        .accordion-item {
            border: none;
            margin-bottom: 10px;
            background: transparent;
        }

        .accordion-button {
            background: white;
            border-radius: var(--border-radius) !important;
            padding: 15px 20px;
            font-weight: 500;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all var(--transition-speed) ease;
        }

        .accordion-button:not(.collapsed) {
            background: white;
            color: var(--primary-color);
        }

        .accordion-button:focus {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .accordion-button::after {
            transition: transform var(--transition-speed) ease;
        }

        .accordion-body {
            padding: 20px;
            background: white;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
        }

        .list-group-item {
            border: none;
            padding: 12px 15px;
            margin-bottom: 5px;
            border-radius: var(--border-radius);
            transition: all var(--transition-speed) ease;
            background: #f8f9fa;
        }

        .list-group-item:hover {
            transform: translateX(5px);
            background: #e9ecef;
        }

        .list-group-item i {
            transition: transform var(--transition-speed) ease;
        }

        .list-group-item:hover i {
            transform: scale(1.2);
        }

        /* Loading Animation */
        .spinner-grow {
            color: var(--primary-color);
        }

        .analyzing {
            color: #666;
            margin-top: 10px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <?php include('fnav.php'); ?>

    <div class="main-container">
        <div class="page-header">
            <h2 style="margin-top: 20px; font-size: 2.5rem;"><i class="fas fa-leaf text-success"></i> Plant Disease Detection</h2>
            <p>Upload a clear image of your plant to detect potential diseases and get expert analysis</p>
        </div>

        <div class="row">
            <div class="col-md-7">
                <div class="upload-container">
                    <div class="drop-zone">
                        <span class="drop-zone__prompt">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Drop your image here or click to upload</span>
                            <small class="text-muted">Supports: JPG, JPEG, PNG</small>
                        </span>
                        <input type="file" name="image" class="drop-zone__input" accept="image/*">
                        <div class="drop-zone__thumb"></div>
                    </div>

                    <div class="loading">
                        <div class="spinner-grow" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 analyzing">Analyzing your plant image...</p>
                    </div>

                    <div class="result-box">
                        <h4><i class="fas fa-microscope"></i> Analysis Result</h4>
                        <div id="result-content"></div>
                        
                        <!-- New sections for disease management -->
                        <div id="disease-details" class="mt-4" style="display: none;">
                            <div class="accordion" id="diseaseAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#precautionsCollapse">
                                            <i class="fas fa-exclamation-triangle text-warning me-2"></i> Precautions
                                        </button>
                                    </h2>
                                    <div id="precautionsCollapse" class="accordion-collapse collapse show" data-bs-parent="#diseaseAccordion">
                                        <div class="accordion-body" id="precautions-content"></div>
                                    </div>
                                </div>
                                
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#remediesCollapse">
                                            <i class="fas fa-hand-holding-medical text-success me-2"></i> Remedies
                                        </button>
                                    </h2>
                                    <div id="remediesCollapse" class="accordion-collapse collapse" data-bs-parent="#diseaseAccordion">
                                        <div class="accordion-body" id="remedies-content"></div>
                                    </div>
                                </div>
                                
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#medicinesCollapse">
                                            <i class="fas fa-prescription-bottle-alt text-primary me-2"></i> Recommended Medicines
                                        </button>
                                    </h2>
                                    <div id="medicinesCollapse" class="accordion-collapse collapse" data-bs-parent="#diseaseAccordion">
                                        <div class="accordion-body" id="medicines-content"></div>
                                    </div>
                                </div>
                                
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#additionalCollapse">
                                            <i class="fas fa-info-circle text-info me-2"></i> Additional Information
                                        </button>
                                    </h2>
                                    <div id="additionalCollapse" class="accordion-collapse collapse" data-bs-parent="#diseaseAccordion">
                                        <div class="accordion-body" id="additional-content"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="info-card">
                    <h5><i class="fas fa-info-circle"></i> How to Get the Best Results</h5>
                    <ol>
                        <li>Take a clear, well-lit photo of the affected plant part</li>
                        <li>Ensure the image is focused and not blurry</li>
                        <li>Include both healthy and affected areas in the image</li>
                        <li>Avoid shadows or glare in the photo</li>
                        <li>Take multiple photos from different angles if needed</li>
                    </ol>
                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb"></i>
                        <strong>Pro Tip:</strong> Morning light is often best for taking clear plant photos as it provides even lighting without harsh shadows.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.querySelectorAll(".drop-zone__input").forEach((inputElement) => {
            const dropZoneElement = inputElement.closest(".drop-zone");

            dropZoneElement.addEventListener("click", (e) => {
                inputElement.click();
            });

            inputElement.addEventListener("change", (e) => {
                if (inputElement.files.length) {
                    updateThumbnail(dropZoneElement, inputElement.files[0]);
                    analyzeImage(inputElement.files[0]);
                }
            });

            dropZoneElement.addEventListener("dragover", (e) => {
                e.preventDefault();
                dropZoneElement.classList.add("drop-zone--over");
            });

            ["dragleave", "dragend"].forEach((type) => {
                dropZoneElement.addEventListener(type, (e) => {
                    dropZoneElement.classList.remove("drop-zone--over");
                });
            });

            dropZoneElement.addEventListener("drop", (e) => {
                e.preventDefault();

                if (e.dataTransfer.files.length) {
                    inputElement.files = e.dataTransfer.files;
                    updateThumbnail(dropZoneElement, e.dataTransfer.files[0]);
                    analyzeImage(e.dataTransfer.files[0]);
                }

                dropZoneElement.classList.remove("drop-zone--over");
            });
        });

        function updateThumbnail(dropZoneElement, file) {
            let thumbnailElement = dropZoneElement.querySelector(".drop-zone__thumb");
            const prompt = dropZoneElement.querySelector(".drop-zone__prompt");

            // First time - remove the prompt
            if (prompt) {
                prompt.remove();
            }

            // First time - there is no thumbnail element, so lets create it
            if (!thumbnailElement) {
                thumbnailElement = document.createElement("div");
                thumbnailElement.classList.add("drop-zone__thumb");
                dropZoneElement.appendChild(thumbnailElement);
            }

            thumbnailElement.dataset.label = file.name;
            thumbnailElement.style.display = "block";

            // Show thumbnail for image files
            if (file.type.startsWith("image/")) {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => {
                    thumbnailElement.style.backgroundImage = `url('${reader.result}')`;
                };
            }
        }

        function analyzeImage(file) {
            $('.loading').show();
            $('.result-box').hide();
            
            let formData = new FormData();
            formData.append('image', file);
            formData.append('farmer_email', '<?php echo $_SESSION['farmer_login_user']; ?>');

            $.ajax({
                url: 'analyze_plant.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('.loading').hide();
                    $('.result-box').show();
                    
                    let result = response;
                    if (typeof response === 'string') {
                        try {
                            result = JSON.parse(response);
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            $('#result-content').html(`
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle"></i>
                                    Error processing the response. Please try again.
                                </div>
                            `);
                            $('#disease-details').hide();
                            return;
                        }
                    }

                    if (result.error) {
                        $('#result-content').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i>
                                ${result.error}
                            </div>
                        `);
                        $('#disease-details').hide();
                        return;
                    }

                    // Display main result
                    const resultHtml = `
                        <div class="alert alert-success">
                            <div class="mb-3">${result.result.replace(/\n/g, '<br>')}</div>
                            ${result.confidence ? `
                                <div class="confidence-badge">
                                    <i class="fas fa-chart-line"></i>
                                    Confidence: ${result.confidence}%
                                </div>
                            ` : ''}
                        </div>
                    `;
                    $('#result-content').html(resultHtml);

                    // Handle disease details if available
                    if (result.disease_details) {
                        $('#disease-details').show();
                        
                        // Precautions
                        if (result.disease_details.precautions && result.disease_details.precautions.length) {
                            const precautionsHtml = result.disease_details.precautions
                                .map(p => `<li class="list-group-item"><i class="fas fa-check text-success me-2"></i>${p}</li>`)
                                .join('');
                            $('#precautions-content').html(`<ul class="list-group list-group-flush">${precautionsHtml}</ul>`);
                        } else {
                            $('#precautions-content').html('<p class="text-muted">No specific precautions available.</p>');
                        }
                        
                        // Remedies
                        if (result.disease_details.remedies && result.disease_details.remedies.length) {
                            const remediesHtml = result.disease_details.remedies
                                .map(r => `<li class="list-group-item"><i class="fas fa-leaf text-success me-2"></i>${r}</li>`)
                                .join('');
                            $('#remedies-content').html(`<ul class="list-group list-group-flush">${remediesHtml}</ul>`);
                        } else {
                            $('#remedies-content').html('<p class="text-muted">No specific remedies available.</p>');
                        }
                        
                        // Medicines
                        if (result.disease_details.medicines && result.disease_details.medicines.length) {
                            const medicinesHtml = result.disease_details.medicines
                                .map(m => `<li class="list-group-item"><i class="fas fa-capsules text-primary me-2"></i>${m}</li>`)
                                .join('');
                            $('#medicines-content').html(`<ul class="list-group list-group-flush">${medicinesHtml}</ul>`);
                        } else {
                            $('#medicines-content').html('<p class="text-muted">No specific medicines available.</p>');
                        }
                        
                        // Additional Information
                        if (result.disease_details.additional_info && result.disease_details.additional_info.length) {
                            const additionalHtml = result.disease_details.additional_info
                                .map(info => `<div class="card-body border-bottom"><i class="fas fa-info-circle text-info me-2"></i>${info}</div>`)
                                .join('');
                            $('#additional-content').html(`<div class="card border-0">${additionalHtml}</div>`);
                        } else {
                            $('#additional-content').html('<p class="text-muted">No additional information available.</p>');
                        }
                    } else {
                        $('#disease-details').hide();
                    }
                },
                error: function() {
                    $('.loading').hide();
                    $('.result-box').show();
                    $('#result-content').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            An error occurred while analyzing the image. Please try again.
                        </div>
                    `);
                    $('#disease-details').hide();
                }
            });
        }
    </script>
</body>
</html>
