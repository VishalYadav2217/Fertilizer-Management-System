console.log('app.js script loaded and running!');

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded and ready!');

    // Crop Info Modal
    const cropInfoBtn = document.getElementById('cropInfoBtn');
    const cropModal = document.getElementById('cropModal');
    const closeModal = document.getElementById('closeModal');
    const cropModalTitle = document.getElementById('cropModalTitle');
    const cropModalContent = document.getElementById('cropModalContent');
    const cropTypeSelect = document.getElementById('cropType');

    if (cropInfoBtn && cropModal && closeModal && cropModalTitle && cropModalContent && cropTypeSelect) {
        console.log('Crop info modal elements found');
        const cropInfo = {
            wheat: { title: 'Wheat', content: 'Wheat thrives in well-drained loamy soil with a pH of 6.0-7.5...' },
            rice: { title: 'Rice', content: 'Rice prefers clayey soil with a pH of 5.5-7.0...' },
            maize: { title: 'Maize', content: 'Maize grows best in loamy soil with a pH of 5.5-7.5...' },
            cotton: { title: 'Cotton', content: 'Cotton prefers well-drained loamy soil with a pH of 5.5-7.5...' },
            pulses: { title: 'Pulses', content: 'Pulses thrive in sandy-loam soil with a pH of 6.0-7.0...' }
        };

        cropInfoBtn.addEventListener('click', () => {
            console.log('Crop info button clicked');
            const selectedCrop = cropTypeSelect.value;
            const info = cropInfo[selectedCrop];
            cropModalTitle.textContent = info.title;
            cropModalContent.textContent = info.content;
            cropModal.classList.remove('hidden');
        });

        closeModal.addEventListener('click', () => {
            console.log('Close modal button clicked');
            cropModal.classList.add('hidden');
        });
    } else {
        console.error('Crop info modal elements missing:', { cropInfoBtn, cropModal, closeModal, cropModalTitle, cropModalContent, cropTypeSelect });
    }

    // Real-time Validation
    const inputs = {
        cropType: { error: 'cropTypeError', validate: (val) => ['wheat', 'rice', 'maize', 'cotton', 'pulses'].includes(val), message: 'Please select a valid crop' },
        soilPh: { error: 'soilPhError', validate: (val) => val >= 4.0 && val <= 9.0, message: 'Soil pH must be between 4.0 and 9.0' },
        nitrogen: { error: 'nitrogenError', validate: (val) => val >= 0, message: 'Nitrogen must be non-negative' },
        phosphorus: { error: 'phosphorusError', validate: (val) => val >= 0, message: 'Phosphorus must be non-negative' },
        potassium: { error: 'potassiumError', validate: (val) => val >= 0, message: 'Potassium must be non-negative' },
        rainfall: { error: 'rainfallError', validate: (val) => val >= 0, message: 'Rainfall must be non-negative' },
        temperature: { error: 'temperatureError', validate: (val) => val >= -10 && val <= 50, message: 'Temperature must be between -10°C and 50°C' },
    };

    Object.keys(inputs).forEach((key) => {
        const input = document.getElementById(key);
        if (input) {
            console.log(`Input ${key} found`);
            input.addEventListener('input', () => {
                console.log(`Input changed for ${key}`);
                const errorSpan = document.getElementById(inputs[key].error);
                const value = key === 'soilPh' ? parseFloat(input.value) : key === 'cropType' ? input.value : parseInt(input.value);
                const isValid = inputs[key].validate(value) || !input.value;
                errorSpan.classList.toggle('hidden', isValid);
                errorSpan.classList.toggle('error-visible', !isValid);
                errorSpan.textContent = isValid ? '' : inputs[key].message;
            });
        } else {
            console.error(`Input element ${key} not found`);
        }
    });

    // Toast Notification
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        if (toast && toastMessage) {
            console.log('Showing toast:', message);
            toastMessage.textContent = message;
            toastMessage.className = `px-4 py-2 rounded-lg shadow-lg text-white animate__animated animate__fadeInRight ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('animate__fadeOutRight');
                setTimeout(() => toast.classList.add('hidden'), 500);
            }, 3000);
        } else {
            console.error('Toast elements not found');
        }
    }

    // Function to parse sustainability tips
    function parseSustainabilityTips(tips) {
        if (Array.isArray(tips)) {
            return tips; // Return array as-is if already an array
        }
        let cleanedText = tips.replace(/\s+/g, ' ').trim();
        let tipsArray = cleanedText.split(/\.[\s\n]/).filter(tip => tip.trim().length > 0);
        return tipsArray.map(tip => tip.replace(/\.$/, '').trim());
    }

    // Function to format growth stages
    function formatGrowthStages(stages) {
        if (Array.isArray(stages) && stages.length > 0) {
            return stages.map(stage => {
                const durationMatch = stage.duration.match(/\d+/); // Extract number from "7 days"
                const duration = durationMatch ? parseInt(durationMatch[0]) : 0;
                return `${stage.name} (${duration} days)`;
            }).join(', ');
        }
        return 'Growth stages data unavailable';
    }

    // Form Submission
    const recommendationForm = document.getElementById('recommendationForm');
    if (recommendationForm) {
        console.log('Recommendation form found');
        recommendationForm.addEventListener('submit', async function (e) {
            console.log('Form submission triggered');
            e.preventDefault();

            let isValid = true;
            Object.keys(inputs).forEach((key) => {
                const input = document.getElementById(key);
                const errorSpan = document.getElementById(inputs[key].error);
                const value = key === 'soilPh' ? parseFloat(input.value) : key === 'cropType' ? input.value : parseInt(input.value);
                const valid = inputs[key].validate(value);
                errorSpan.classList.toggle('hidden', valid);
                errorSpan.classList.toggle('error-visible', !valid);
                errorSpan.textContent = valid ? '' : inputs[key].message;
                if (!valid) isValid = false;
            });

            if (!isValid) {
                console.log('Form validation failed');
                showToast('Please correct the form errors.', 'error');
                return;
            }

            const outputDiv = document.getElementById('recommendationOutput');
            const errorCard = document.getElementById('errorCard');
            const growthPredictionCard = document.getElementById('growthPredictionCard');
            const buttonText = document.getElementById('buttonText');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const progressBar = document.getElementById('progressBar');
            const progress = document.getElementById('progress');

            if (!outputDiv || !errorCard || !growthPredictionCard || !buttonText || !loadingSpinner || !progressBar || !progress) {
                console.error('One or more output elements not found:', { outputDiv, errorCard, growthPredictionCard, buttonText, loadingSpinner, progressBar, progress });
                return;
            }

            console.log('Preparing to show loading state');
            outputDiv.classList.add('hidden');
            errorCard.classList.add('hidden');
            growthPredictionCard.classList.add('hidden');
            progressBar.classList.remove('hidden');

            let progressWidth = 0;
            const progressInterval = setInterval(() => {
                progressWidth = Math.min(progressWidth + 10, 80);
                progress.style.width = `${progressWidth}%`;
            }, 200);

            buttonText.textContent = 'Processing...';
            loadingSpinner.classList.remove('hidden');

            const formData = {
                crop_type: document.getElementById('cropType').value,
                soil_ph: parseFloat(document.getElementById('soilPh').value),
                nitrogen: parseInt(document.getElementById('nitrogen').value),
                phosphorus: parseInt(document.getElementById('phosphorus').value),
                potassium: parseInt(document.getElementById('potassium').value),
                rainfall: parseInt(document.getElementById('rainfall').value),
                temperature: parseInt(document.getElementById('temperature').value),
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            console.log('CSRF Token:', csrfToken);

            try {
                console.log('Sending fetch request with data:', formData);
                const response = await fetch('http://127.0.0.1:8000/recommend', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(formData),
                });

                const data = await response.json();
                console.log('Recommendation Data Received:', data);

                clearInterval(progressInterval);
                progress.style.width = '100%';
                setTimeout(() => {
                    progressBar.classList.add('hidden');
                    progress.style.width = '0%';
                }, 500);

                buttonText.textContent = 'Get Recommendation';
                loadingSpinner.classList.add('hidden');

                if (response.ok) {
                    // Update fertilizer details
                    const fertilizerType = document.getElementById('fertilizerType');
                    const quantity = document.getElementById('quantity');
                    if (fertilizerType && quantity) {
                        console.log('Updating fertilizer details');
                        fertilizerType.textContent = `Fertilizer Type: ${data.fertilizer_type || 'N/A'}`;
                        quantity.textContent = `Quantity: ${data.quantity || 'N/A'} kg/ha`;
                    } else {
                        console.error('Fertilizer elements not found');
                    }

                    // Update sustainability tips
                    const tipsList = document.getElementById('sustainabilityTips');
                    if (tipsList) {
                        console.log('Updating sustainability tips');
                        tipsList.innerHTML = '';
                        const tips = parseSustainabilityTips(data.sustainability_tips || '');
                        tips.forEach(tip => {
                            const li = document.createElement('li');
                            li.textContent = tip;
                            tipsList.appendChild(li);
                        });
                    } else {
                        console.error('Sustainability tips list not found');
                    }

                    // Update growth prediction
                    const growthStages = document.getElementById('growthStages');
                    const estimatedYield = document.getElementById('estimatedYield');
                    if (growthStages && estimatedYield) {
                        console.log('Growth Stages Data:', data.growth_stages);
                        console.log('Estimated Yield Data:', data.estimated_yield);
                        const formattedStages = formatGrowthStages(data.growth_stages);
                        console.log('Formatted Stages:', formattedStages);
                        growthStages.textContent = `Growth Stages: ${formattedStages}`;
                        estimatedYield.textContent = `Estimated Yield: ${data.estimated_yield || 'Data unavailable'} tons/ha`;
                    } else {
                        console.error('Growth prediction elements not found:', { growthStages, estimatedYield });
                    }

                    outputDiv.classList.remove('hidden');
                    growthPredictionCard.classList.remove('hidden');
                    outputDiv.classList.add('animate__animated', 'animate__fadeIn');

                    showToast('Recommendation and prediction generated successfully!', 'success');
                } else {
                    console.log('Response not OK, showing error');
                    document.getElementById('error').textContent = `Error: ${data.error || 'Failed to fetch recommendation'}`;
                    errorCard.classList.remove('hidden');
                    outputDiv.classList.remove('hidden');
                    showToast(`Error: ${data.error || 'Failed to fetch recommendation'}`, 'error');
                }
            } catch (error) {
                console.log('Catch block triggered due to error:', error);
                clearInterval(progressInterval);
                progressBar.classList.add('hidden');
                progress.style.width = '0%';
                buttonText.textContent = 'Get Recommendation';
                loadingSpinner.classList.add('hidden');
                document.getElementById('error').textContent = 'Error: Unable to connect to the server';
                errorCard.classList.remove('hidden');
                outputDiv.classList.remove('hidden');
                console.error('Fetch error:', error);
                showToast(`Error: Unable to connect to the server - ${error.message}`, 'error');
            }
        });
    } else {
        console.error('Recommendation form not found');
    }
});