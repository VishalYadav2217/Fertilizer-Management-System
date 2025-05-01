<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Fertilizer Recommendation System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <style>
        :root {
            --primary: #4CAF50;
            --secondary: #2196F3;
            --neon-glow: #00FF88;
            --background-light: #E8F5E9;
            --background-dark: #0A0F1B;
            --text-light: #2D3748;
            --text-dark: #E2E8F0;
            --card-bg-light: rgba(255, 255, 255, 0.1);
            --card-bg-dark: rgba(20, 30, 50, 0.8);
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--background-light), #BBDEFB);
            color: var(--text-light);
            transition: all 0.5s ease;
            overflow-x: hidden;
            position: relative;
        }
        body.dark {
            background: linear-gradient(135deg, var(--background-dark), #1C2526);
            color: var(--text-dark);
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(76, 175, 80, 0.1) 0%, transparent 70%);
            z-index: -1;
        }
        header {
            position: relative;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            padding: 4rem 1rem 8rem 1rem;
            text-align: center;
            color: white;
            border-bottom-left-radius: 3rem;
            border-bottom-right-radius: 3rem;
            overflow: hidden;
            box-shadow: 0 0 30px rgba(0, 255, 136, 0.5);
        }
        .wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 120px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='0.3' d='M0,192L48,176C96,160,192,128,288,138.7C384,149,480,203,576,213.3C672,224,768,192,864,181.3C960,171,1056,181,1152,197.3C1248,213,1344,235,1392,245.3L1440,256L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
            background-size: cover;
            animation: none;
            filter: drop-shadow(0 0 10px rgba(0, 255, 136, 0.3));
        }
        header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 3rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        header p {
            font-size: 1.2rem;
            letter-spacing: 1px;
        }
        .card {
            background: var(--card-bg-light);
            backdrop-filter: blur(15px);
            border-radius: 1.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2), inset 0 0 10px rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: transform 0.5s ease, box-shadow 0.5s ease, border 0.5s ease;
            position: relative;
            overflow: hidden;
        }
        body.dark .card {
            background: var(--card-bg-dark);
            border: 1px solid rgba(0, 255, 136, 0.2);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5), inset 0 0 10px rgba(0, 255, 136, 0.1);
        }
        .card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 50px rgba(0, 255, 136, 0.3), inset 0 0 15px rgba(255, 255, 255, 0.2);
            border: 1px solid var(--neon-glow);
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(0, 255, 136, 0.1), transparent);
            opacity: 0;
            transition: opacity 0.5s ease;
        }
        .card:hover::before {
            opacity: 1;
        }
        .input-field {
            border: 2px solid #4CAF50;
            border-radius: 0.75rem;
            padding: 0.75rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-light);
            box-shadow: inset 0 0 5px rgba(0, 255, 136, 0.2);
        }
        body.dark .input-field {
            border: 2px solid #00FF88;
            background: rgba(20, 30, 50, 0.5);
            color: var(--text-dark);
        }
        .input-field:focus {
            border-color: var(--neon-glow);
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.5), inset 0 0 10px rgba(0, 255, 136, 0.3);
            outline: none;
        }
        .btn-primary {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.5);
            position: relative;
            overflow: hidden;
        }
        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 0 25px rgba(0, 255, 136, 0.8);
            background: linear-gradient(90deg, var(--secondary), var(--primary));
        }
        .btn-primary::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .btn-primary:hover::after {
            opacity: 1;
        }
        #toastMessage {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            color: white;
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.5);
            border: 1px solid var(--neon-glow);
        }
        #progress {
            background: var(--neon-glow);
            height: 100%;
            transition: width 0.3s ease;
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.8);
        }
        #progressBar {
            background: rgba(255, 255, 255, 0.1);
        }
        body.dark #progressBar {
            background: rgba(0, 255, 136, 0.1);
        }
        .error-visible {
            color: #FF5555;
            background: rgba(255, 85, 85, 0.2);
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            box-shadow: 0 0 10px rgba(255, 85, 85, 0.5);
        }
        #recCard {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(243, 244, 246, 0.05));
            padding: 2rem;
            border-left: 4px solid var(--neon-glow);
            min-height: 300px;
            width: 100%;
            box-sizing: border-box;
            box-shadow: inset 0 0 15px rgba(0, 255, 136, 0.3);
        }
        body.dark #recCard {
            background: linear-gradient(135deg, rgba(20, 30, 50, 0.8), rgba(74, 85, 104, 0.2));
        }
        #recCard h3 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--neon-glow);
            margin-bottom: 1.5rem;
        }
        #recCard p {
            font-size: 1.2rem;
            color: var(--text-light);
            line-height: 1.75;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            background: rgba(76, 175, 80, 0.2);
            border-radius: 0.5rem;
            display: inline-block;
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.3);
        }
        body.dark #recCard p {
            color: var(--text-dark);
            background: rgba(76, 175, 80, 0.3);
        }
        #sustainabilityTips {
            list-style: none;
            padding-left: 0;
            margin-top: 1.5rem;
        }
        #sustainabilityTips li {
            position: relative;
            padding-left: 2rem;
            margin-bottom: 1rem;
            color: var(--text-light);
            font-size: 1.1rem;
            line-height: 1.6;
        }
        body.dark #sustainabilityTips li {
            color: var(--text-dark);
        }
        #sustainabilityTips li:hover {
            color: var(--neon-glow);
        }
        #sustainabilityTips li:before {
            content: "🌱";
            position: absolute;
            left: 0;
            color: var(--neon-glow);
            font-size: 1.2rem;
        }
        #growthPredictionCard {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(224, 242, 254, 0.05));
            padding: 2rem;
            border-left: 4px solid var(--secondary);
            min-height: 300px; /* Match recCard height */
            width: 100%;
            box-sizing: border-box;
            box-shadow: inset 0 0 15px rgba(33, 150, 243, 0.3);
        }
        body.dark #growthPredictionCard {
            background: linear-gradient(135deg, rgba(20, 30, 50, 0.8), rgba(66, 153, 225, 0.3));
        }
        #growthPredictionCard h3 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 1.5rem;
        }
        #growthPredictionCard p {
            font-size: 1.2rem;
            color: var(--text-light);
            line-height: 1.75;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            background: rgba(33, 150, 243, 0.2);
            border-radius: 0.5rem;
            display: inline-block;
            box-shadow: 0 0 10px rgba(33, 150, 243, 0.3);
        }
        body.dark #growthPredictionCard p {
            color: var(--text-dark);
            background: rgba(33, 150, 243, 0.3);
        }
        .theme-toggle {
            position: absolute;
            top: 1rem;
            right: 1rem;
            cursor: pointer;
            font-size: 1.5rem;
            color: white;
            transition: transform 0.3s ease, color 0.3s ease;
        }
        .theme-toggle:hover {
            transform: rotate(180deg);
            color: var(--neon-glow);
        }
        .download-btn {
            background: linear-gradient(90deg, #FF9800, #F44336);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        .download-btn:hover {
            transform: scale(1.05);
            background: linear-gradient(90deg, #F44336, #FF9800);
        }
        #cropModal {
            backdrop-filter: blur(10px);
            background: rgba(0, 0, 0, 0.7);
        }
        #cropModal > div {
            background: var(--card-bg-light);
            border: 1px solid var(--neon-glow);
        }
        body.dark #cropModal > div {
            background: var(--card-bg-dark);
        }
        #cropModal h2 {
            font-family: 'Orbitron', sans-serif;
            color: var(--neon-glow);
        }
        #cropModal p {
            color: var(--text-light);
        }
        body.dark #cropModal p {
            color: var(--text-dark);
        }
        #closeModal i {
            transition: color 0.3s ease;
        }
        #closeModal:hover i {
            color: var(--neon-glow);
        }
        label {
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 1px;
            color: var(--text-light);
        }
        body.dark label {
            color: var(--text-dark);
        }
        label[for="soilPh"] span.brackets {
            opacity: 1;
        }
        body.light label[for="soilPh"] span.brackets {
            opacity: 0;
        }
        #recommendationOutput h2 {
            font-family: 'Orbitron', sans-serif;
            color: var(--neon-glow);
        }
        form, .input-field, label, select, input, #recommendationOutput, #recCard, #growthPredictionCard, #toast, #cropModal, .card, header h1, header p {
            animation: none !important;
            transition: none !important;
        }
        .card {
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body class="min-h-screen">
    <div id="toast" class="fixed top-4 right-4 z-50 hidden">
        <div id="toastMessage"></div>
    </div>
    <header class="relative">
        <div class="wave"></div>
        <h1 class="text-4xl md:text-5xl font-bold">Fertilizer Recommendation System</h1>
        <p class="mt-2 text-lg md:text-xl text-gray-100">AI-Powered Insights for Sustainable Farming</p>
        <div class="theme-toggle" onclick="toggleTheme()">
            <i class="fas fa-moon"></i>
        </div>
    </header>
    <main class="container mx-auto px-4 py-12 -mt-12">
        <div class="card p-6 md:p-8">
            <div id="cropModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl max-w-md w-full">
                    <div class="flex justify-between items-center mb-4">
                        <h2 id="cropModalTitle" class="text-xl font-semibold text-gray-800 dark:text-gray-200"></h2>
                        <button id="closeModal" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <p id="cropModalContent" class="text-gray-700 dark:text-gray-300"></p>
                </div>
            </div>
            <form id="recommendationForm" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="relative">
                    <label for="cropType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 flex items-center">
                        Crop Type
                        <button type="button" id="cropInfoBtn" class="ml-2 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-600">
                            <i class="fas fa-info-circle"></i>
                        </button>
                    </label>
                    <select id="cropType" name="crop_type" class="input-field w-full bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-200">
                        <option value="wheat">Wheat</option>
                        <option value="rice">Rice</option>
                        <option value="maize">Maize</option>
                        <option value="cotton">Cotton</option>
                        <option value="pulses">Pulses</option>
                    </select>
                    <span class="absolute right-2 top-12 text-red-500 hidden" id="cropTypeError"></span>
                </div>
                <div class="relative">
                    <label for="soilPh" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 flex items-center">
                        Soil pH <span class="brackets">[4.0-9.0]</span>
                    </label>
                    <input type="number" step="0.1" min="4.0" max="9.0" id="soilPh" name="soil_ph" class="input-field w-full" required>
                    <span class="absolute right-2 top-12 text-red-500 hidden" id="soilPhError"></span>
                </div>
                <div class="relative">
                    <label for="nitrogen" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nitrogen (ppm)</label>
                    <input type="number" min="0" id="nitrogen" name="nitrogen" class="input-field w-full" required>
                    <span class="absolute right-2 top-12 text-red-500 hidden" id="nitrogenError"></span>
                </div>
                <div class="relative">
                    <label for="phosphorus" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phosphorus (ppm)</label>
                    <input type="number" min="0" id="phosphorus" name="phosphorus" class="input-field w-full" required>
                    <span class="absolute right-2 top-12 text-red-500 hidden" id="phosphorusError"></span>
                </div>
                <div class="relative">
                    <label for="potassium" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Potassium (ppm)</label>
                    <input type="number" min="0" id="potassium" name="potassium" class="input-field w-full" required>
                    <span class="absolute right-2 top-12 text-red-500 hidden" id="potassiumError"></span>
                </div>
                <div class="relative">
                    <label for="rainfall" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Annual Rainfall (mm)</label>
                    <input type="number" min="0" id="rainfall" name="rainfall" class="input-field w-full" required>
                    <span class="absolute right-2 top-12 text-red-500 hidden" id="rainfallError"></span>
                </div>
                <div class="relative">
                    <label for="temperature" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Average Temperature (°C)</label>
                    <input type="number" min="-10" max="50" id="temperature" name="temperature" class="input-field w-full" required>
                    <span class="absolute right-2 top-12 text-red-500 hidden" id="temperatureError"></span>
                </div>
                <div class="col-span-1 md:col-span-2 mt-4">
                    <div id="progressBar" class="hidden h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mb-4">
                        <div id="progress" style="width: 0%"></div>
                    </div>
                    <button type="submit" class="btn-primary w-full flex items-center justify-center">
                        <span id="buttonText">Get Recommendation</span>
                        <svg id="loadingSpinner" class="animate-spin h-5 w-5 ml-2 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
            <div id="recommendationOutput" class="mt-8 hidden">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl md:text-3xl font-semibold text-gray-800 dark:text-gray-200">Recommendation</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div id="recCard" class="p-6">
                        <h3 class="text-lg font-semibold text-green-700 dark:text-green-400 mb-2">Fertilizer Details</h3>
                        <p id="fertilizerType" class="text-gray-700 dark:text-gray-300 font-medium"></p>
                        <p id="quantity" class="text-gray-700 dark:text-gray-300 mt-2"></p>
                        <div class="mt-4">
                            <h4 class="text-lg font-semibold text-green-700 dark:text-green-400">Sustainability Tips</h4>
                            <ul id="sustainabilityTips" class="mt-2"></ul>
                        </div>
                    </div>
                    <div id="growthPredictionCard" class="p-6">
                        <h3 class="text-lg font-semibold text-blue-700 dark:text-blue-400 mb-2">Crop Growth Prediction</h3>
                        <p id="growthStages" class="text-gray-700 dark:text-gray-300"></p>
                        <p id="estimatedYield" class="text-gray-700 dark:text-gray-300 mt-2"></p>
                    </div>
                    <div id="errorCard" class="card p-6 bg-red-50 dark:bg-red-900 hidden">
                        <p id="error" class="text-lg text-red-600 dark:text-red-300"></p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script>
        // Theme Toggle
        function toggleTheme() {
            const body = document.body;
            body.classList.toggle('dark');
            const icon = document.querySelector('.theme-toggle i');
            icon.classList.toggle('fa-moon');
            icon.classList.toggle('fa-sun');
            localStorage.setItem('theme', body.classList.contains('dark') ? 'dark' : 'light');
        }

        // Load Theme Preference
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'light';
            if (savedTheme === 'dark') {
                document.body.classList.add('dark');
                document.querySelector('.theme-toggle i').classList.replace('fa-moon', 'fa-sun');
            }

            // PDF Download
            const downloadBtn = document.getElementById('downloadBtn');
            if (downloadBtn) {
                downloadBtn.addEventListener('click', () => {
                    const element = document.getElementById('recommendationOutput');
                    const opt = {
                        margin: 0.5,
                        filename: 'Fertilizer_Recommendation.pdf',
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: { scale: 2 },
                        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
                    };
                    html2pdf().set(opt).from(element).save();
                });
            }

            // Show Download Button When Recommendation is Generated
            const recommendationOutput = document.getElementById('recommendationOutput');
            const observer = new MutationObserver(() => {
                if (downloadBtn) {
                    if (recommendationOutput.classList.contains('hidden')) {
                        downloadBtn.classList.add('hidden');
                    } else {
                        downloadBtn.classList.remove('hidden');
                    }
                }
            });
            observer.observe(recommendationOutput, { attributes: true });
        });
    </script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>