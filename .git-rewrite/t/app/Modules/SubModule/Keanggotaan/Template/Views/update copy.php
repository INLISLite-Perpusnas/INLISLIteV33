<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template Kartu Editor</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .editor-container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .editor-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
        }

        .editor-header h1 {
            margin: 0;
            font-size: 24px;
        }

        .editor-content {
            display: flex;
            height: 80vh;
        }

        .sidebar {
            width: 300px;
            background: #f8f9fa;
            border-right: 1px solid #dee2e6;
            padding: 20px;
            overflow-y: auto;
        }

        .main-editor {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .toolbar {
            background: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .canvas-container {
            flex: 1;
            padding: 20px;
            background: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: auto;
        }

        .card-canvas {
            position: relative;
            width: 400px;
            height: 250px;
            background: white;
            border: 2px solid #ccc;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            background-size: cover;
            background-position: center;
            cursor: crosshair;
        }

        .card-canvas.landscape {
            width: 400px;
            height: 250px;
        }

        .card-canvas.portrait {
            width: 250px;
            height: 400px;
        }

        .element-item {
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            cursor: grab;
            transition: all 0.3s ease;
        }

        .element-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .element-item.dragging {
            opacity: 0.5;
            cursor: grabbing;
        }

        .draggable-element {
            position: absolute;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid #007bff;
            border-radius: 4px;
            padding: 5px 8px;
            cursor: move;
            user-select: none;
            font-size: 12px;
            min-width: 80px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .draggable-element:hover {
            background: rgba(255, 255, 255, 1);
            border-color: #0056b3;
        }

        .draggable-element.selected {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.1);
        }

        .element-controls {
            position: absolute;
            top: -30px;
            right: 0;
            display: none;
        }

        .draggable-element.selected .element-controls {
            display: block;
        }

        .control-btn {
            background: #dc3545;
            color: white;
            border: none;
            width: 20px;
            height: 20px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .elements-section h3 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .background-upload {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .background-upload:hover {
            border-color: #007bff;
            background: rgba(0, 123, 255, 0.05);
        }

        .background-upload.dragover {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.1);
        }

        .property-panel {
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
            display: none;
        }

        .property-panel.active {
            display: block;
        }

        .canvas-guides {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            border: 1px dashed rgba(0,0,0,0.2);
        }

        .guide-line {
            position: absolute;
            background: rgba(0, 123, 255, 0.3);
            display: none;
        }

        .guide-line.vertical {
            width: 1px;
            height: 100%;
        }

        .guide-line.horizontal {
            height: 1px;
            width: 100%;
        }

        #jsonOutput {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="editor-container">
        <div class="editor-header">
            <h1><i class="fas fa-id-card"></i> Template Kartu Editor</h1>
            <p>Drag elemen dari sidebar dan posisikan di kanvas kartu</p>
        </div>
        
        <div class="editor-content">
            <div class="sidebar">
                <!-- Form Settings -->
                <div class="form-group">
                    <label>Kategori</label>
                    <select class="form-control" id="category">
                        <option value="landscape">Landscape</option>
                        <option value="portrait">Portrait</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Sub Kategori</label>
                    <select class="form-control" id="subcategory">
                        <option value="depan">Depan</option>
                        <option value="belakang">Belakang</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Layout</label>
                    <select class="form-control" id="layout">
                        <option value="landscape">Landscape</option>
                        <option value="portrait">Portrait</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nama Template</label>
                    <input type="text" class="form-control" id="templateName" placeholder="Nama Template">
                </div>

                <!-- Background Upload -->
                <div class="background-upload" id="backgroundUpload">
                    <i class="fas fa-cloud-upload-alt fa-2x"></i>
                    <p>Klik atau drag background image</p>
                    <input type="file" id="backgroundFile" accept="image/*" style="display: none;">
                </div>

                <!-- Available Elements -->
                <div class="elements-section">
                    <h3>Elemen Perpustakaan</h3>
                    <div class="element-item" data-element="perpus_nama">
                        <i class="fas fa-building"></i> Nama Perpustakaan
                    </div>
                    <div class="element-item" data-element="perpus_alamat">
                        <i class="fas fa-map-marker-alt"></i> Alamat Perpustakaan
                    </div>
                    <div class="element-item" data-element="perpus_logo">
                        <i class="fas fa-image"></i> Logo Perpustakaan
                    </div>
                </div>

                <div class="elements-section">
                    <h3>Elemen Anggota</h3>
                    <div class="element-item" data-element="anggota_qrcode">
                        <i class="fas fa-qrcode"></i> QR Code Anggota
                    </div>
                    <div class="element-item" data-element="anggota_foto">
                        <i class="fas fa-user-circle"></i> Foto Anggota
                    </div>
                    <div class="element-item" data-element="anggota_nomor">
                        <i class="fas fa-hashtag"></i> Nomor Anggota
                    </div>
                    <div class="element-item" data-element="anggota_nama">
                        <i class="fas fa-user"></i> Nama Anggota
                    </div>
                    <div class="element-item" data-element="anggota_jenis">
                        <i class="fas fa-tags"></i> Jenis Anggota
                    </div>
                </div>

                <!-- Property Panel -->
                <div class="property-panel" id="propertyPanel">
                    <h4>Properties</h4>
                    <div class="form-group">
                        <label>Font Size</label>
                        <input type="number" class="form-control" id="fontSize" min="8" max="72" value="12">
                    </div>
                    <div class="form-group">
                        <label>Font Color</label>
                        <input type="color" class="form-control" id="fontColor" value="#000000">
                    </div>
                    <div class="form-group">
                        <label>Background Color</label>
                        <input type="color" class="form-control" id="bgColor" value="#ffffff">
                    </div>
                    <div class="form-group">
                        <label>Width</label>
                        <input type="number" class="form-control" id="elementWidth" min="50" max="200">
                    </div>
                    <div class="form-group">
                        <label>Height</label>
                        <input type="number" class="form-control" id="elementHeight" min="20" max="100">
                    </div>
                </div>
            </div>

            <div class="main-editor">
                <div class="toolbar">
                    <button class="btn btn-primary" id="clearCanvas">
                        <i class="fas fa-trash"></i> Clear
                    </button>
                    <button class="btn btn-secondary" id="previewBtn">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                    <button class="btn btn-success" id="saveTemplate">
                        <i class="fas fa-save"></i> Save Template
                    </button>
                </div>

                <div class="canvas-container">
                    <div class="card-canvas landscape" id="cardCanvas">
                        <div class="canvas-guides">
                            <div class="guide-line vertical" style="left: 50%;"></div>
                            <div class="guide-line horizontal" style="top: 50%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Output JSON (hidden, for debugging) -->
    <div id="jsonOutput" style="display: none;"></div>

    <script>
        class TemplateEditor {
            constructor() {
                this.canvas = document.getElementById('cardCanvas');
                this.selectedElement = null;
                this.elements = [];
                this.dragOffset = { x: 0, y: 0 };
                this.backgroundImage = null;
                
                this.init();
            }

            init() {
                this.setupEventListeners();
                this.setupDragAndDrop();
                this.updateCanvasSize();
            }

            setupEventListeners() {
                // Layout change
                document.getElementById('layout').addEventListener('change', (e) => {
                    this.updateCanvasSize();
                });

                // Background upload
                document.getElementById('backgroundUpload').addEventListener('click', () => {
                    document.getElementById('backgroundFile').click();
                });

                document.getElementById('backgroundFile').addEventListener('change', (e) => {
                    this.handleBackgroundUpload(e.target.files[0]);
                });

                // Toolbar buttons
                document.getElementById('clearCanvas').addEventListener('click', () => {
                    this.clearCanvas();
                });

                document.getElementById('saveTemplate').addEventListener('click', () => {
                    this.saveTemplate();
                });

                document.getElementById('previewBtn').addEventListener('click', () => {
                    this.previewTemplate();
                });

                // Property panel
                document.getElementById('fontSize').addEventListener('change', () => {
                    this.updateSelectedElement();
                });

                document.getElementById('fontColor').addEventListener('change', () => {
                    this.updateSelectedElement();
                });

                document.getElementById('bgColor').addEventListener('change', () => {
                    this.updateSelectedElement();
                });

                document.getElementById('elementWidth').addEventListener('change', () => {
                    this.updateSelectedElement();
                });

                document.getElementById('elementHeight').addEventListener('change', () => {
                    this.updateSelectedElement();
                });
            }

            setupDragAndDrop() {
                // Make element items draggable
                document.querySelectorAll('.element-item').forEach(item => {
                    item.addEventListener('dragstart', (e) => {
                        e.dataTransfer.setData('text/plain', item.dataset.element);
                        item.classList.add('dragging');
                    });

                    item.addEventListener('dragend', () => {
                        item.classList.remove('dragging');
                    });

                    item.setAttribute('draggable', 'true');
                });

                // Canvas drop zone
                this.canvas.addEventListener('dragover', (e) => {
                    e.preventDefault();
                });

                this.canvas.addEventListener('drop', (e) => {
                    e.preventDefault();
                    const elementType = e.dataTransfer.getData('text/plain');
                    const rect = this.canvas.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    
                    this.createElement(elementType, x, y);
                });
            }

            updateCanvasSize() {
                const layout = document.getElementById('layout').value;
                this.canvas.className = `card-canvas ${layout}`;
            }

            handleBackgroundUpload(file) {
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.backgroundImage = e.target.result;
                        this.canvas.style.backgroundImage = `url(${e.target.result})`;
                    };
                    reader.readAsDataURL(file);
                }
            }

            createElement(type, x, y) {
                const element = document.createElement('div');
                element.className = 'draggable-element';
                element.dataset.type = type;
                element.innerHTML = `
                    {${type}}
                    <div class="element-controls">
                        <button class="control-btn" onclick="templateEditor.removeElement(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                
                element.style.left = x + 'px';
                element.style.top = y + 'px';

                // Make element draggable within canvas
                this.makeElementDraggable(element);
                
                // Add click handler for selection
                element.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.selectElement(element);
                });

                this.canvas.appendChild(element);
                this.elements.push({
                    id: Date.now(),
                    type: type,
                    x: x,
                    y: y,
                    width: 80,
                    height: 25,
                    fontSize: 12,
                    fontColor: '#000000',
                    backgroundColor: '#ffffff'
                });

                this.selectElement(element);
            }

            makeElementDraggable(element) {
                let isDragging = false;
                let startX, startY, initialLeft, initialTop;

                element.addEventListener('mousedown', (e) => {
                    if (e.target.classList.contains('control-btn')) return;
                    
                    isDragging = true;
                    startX = e.clientX;
                    startY = e.clientY;
                    initialLeft = parseInt(element.style.left);
                    initialTop = parseInt(element.style.top);
                    
                    document.addEventListener('mousemove', onMouseMove);
                    document.addEventListener('mouseup', onMouseUp);
                    
                    e.preventDefault();
                });

                const onMouseMove = (e) => {
                    if (!isDragging) return;
                    
                    const deltaX = e.clientX - startX;
                    const deltaY = e.clientY - startY;
                    
                    const newLeft = Math.max(0, Math.min(this.canvas.offsetWidth - element.offsetWidth, initialLeft + deltaX));
                    const newTop = Math.max(0, Math.min(this.canvas.offsetHeight - element.offsetHeight, initialTop + deltaY));
                    
                    element.style.left = newLeft + 'px';
                    element.style.top = newTop + 'px';
                };

                const onMouseUp = () => {
                    isDragging = false;
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);
                    
                    // Update element data
                    const elementData = this.elements.find(el => el.type === element.dataset.type);
                    if (elementData) {
                        elementData.x = parseInt(element.style.left);
                        elementData.y = parseInt(element.style.top);
                    }
                };
            }

            selectElement(element) {
                // Remove previous selection
                if (this.selectedElement) {
                    this.selectedElement.classList.remove('selected');
                }

                // Select new element
                this.selectedElement = element;
                element.classList.add('selected');

                // Show property panel
                this.showPropertyPanel(element);
            }

            showPropertyPanel(element) {
                const panel = document.getElementById('propertyPanel');
                const elementData = this.elements.find(el => el.type === element.dataset.type);
                
                if (elementData) {
                    document.getElementById('fontSize').value = elementData.fontSize;
                    document.getElementById('fontColor').value = elementData.fontColor;
                    document.getElementById('bgColor').value = elementData.backgroundColor;
                    document.getElementById('elementWidth').value = elementData.width;
                    document.getElementById('elementHeight').value = elementData.height;
                }

                panel.classList.add('active');
            }

            updateSelectedElement() {
                if (!this.selectedElement) return;

                const elementData = this.elements.find(el => el.type === this.selectedElement.dataset.type);
                if (!elementData) return;

                // Update data
                elementData.fontSize = parseInt(document.getElementById('fontSize').value);
                elementData.fontColor = document.getElementById('fontColor').value;
                elementData.backgroundColor = document.getElementById('bgColor').value;
                elementData.width = parseInt(document.getElementById('elementWidth').value);
                elementData.height = parseInt(document.getElementById('elementHeight').value);

                // Apply styles
                this.selectedElement.style.fontSize = elementData.fontSize + 'px';
                this.selectedElement.style.color = elementData.fontColor;
                this.selectedElement.style.backgroundColor = elementData.backgroundColor;
                this.selectedElement.style.width = elementData.width + 'px';
                this.selectedElement.style.height = elementData.height + 'px';
            }

            removeElement(button) {
                const element = button.closest('.draggable-element');
                const elementType = element.dataset.type;
                
                // Remove from DOM
                element.remove();
                
                // Remove from data
                this.elements = this.elements.filter(el => el.type !== elementType);
                
                // Hide property panel if this was selected
                if (this.selectedElement === element) {
                    this.selectedElement = null;
                    document.getElementById('propertyPanel').classList.remove('active');
                }
            }

            clearCanvas() {
                this.canvas.querySelectorAll('.draggable-element').forEach(el => el.remove());
                this.elements = [];
                this.selectedElement = null;
                document.getElementById('propertyPanel').classList.remove('active');
            }

            previewTemplate() {
                const templateData = this.generateTemplateData();
                document.getElementById('jsonOutput').style.display = 'block';
                document.getElementById('jsonOutput').textContent = JSON.stringify(templateData, null, 2);
            }

            generateTemplateData() {
                return {
                    category: document.getElementById('category').value,
                    subcategory: document.getElementById('subcategory').value,
                    layout: document.getElementById('layout').value,
                    title: document.getElementById('templateName').value,
                    background: this.backgroundImage,
                    canvas: {
                        width: this.canvas.offsetWidth,
                        height: this.canvas.offsetHeight
                    },
                    elements: this.elements
                };
            }

            saveTemplate() {
                const templateData = this.generateTemplateData();
                
                // You can send this to your PHP backend
                console.log('Template Data:', templateData);
                
                // Example AJAX call (uncomment and modify as needed)
                /*
                fetch('your-save-endpoint.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(templateData)
                })
                .then(response => response.json())
                .then(data => {
                    alert('Template saved successfully!');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error saving template');
                });
                */
                
                alert('Template data ready to save! Check console for details.');
            }
        }

        // Initialize the editor
        const templateEditor = new TemplateEditor();

        // Canvas click handler to deselect elements
        document.getElementById('cardCanvas').addEventListener('click', (e) => {
            if (e.target === e.currentTarget && templateEditor.selectedElement) {
                templateEditor.selectedElement.classList.remove('selected');
                templateEditor.selectedElement = null;
                document.getElementById('propertyPanel').classList.remove('active');
            }
        });
    </script>
</body>
</html>