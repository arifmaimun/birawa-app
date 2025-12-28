<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 md:p-8">
                
                <div class="mb-6 border-b border-slate-100 pb-4">
                    <h1 class="text-2xl font-bold text-birawa-700">Medical Consent Form</h1>
                    <p class="text-sm text-slate-500 mt-1">Please read carefully and sign below to authorize treatment.</p>
                </div>
                
                <!-- Scrollable Consent Text -->
                <div class="bg-slate-50 p-5 rounded-2xl h-64 overflow-y-auto mb-8 border border-slate-200 text-sm text-slate-700 leading-relaxed shadow-inner">
                    <p class="mb-3"><strong class="text-slate-900">1. Authorization for Treatment:</strong> I hereby authorize the veterinarian to examine, prescribe for, and treat the above-described pet. I assume responsibility for all charges incurred in the care of this animal.</p>
                    <p class="mb-3"><strong class="text-slate-900">2. Anesthetic Risk:</strong> I understand that while all due care will be taken, there is always a risk involved with anesthesia and surgery, including death.</p>
                    <p class="mb-3"><strong class="text-slate-900">3. Financial Policy:</strong> Full payment is due at the time services are rendered. I understand that a deposit may be required for hospitalization or surgery.</p>
                    <p class="mb-3"><strong class="text-slate-900">4. Emergency Care:</strong> In the event of an emergency, I authorize the staff to perform any necessary life-saving procedures until I can be reached.</p>
                    <!-- Add more dummy text to make it scrollable -->
                    <br>
                    <p class="mb-3">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                    <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                </div>

                <form action="#" method="POST" id="consentForm">
                    @csrf
                    
                    <!-- Client Signature -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Client Signature</label>
                        <div class="border-2 border-dashed border-slate-300 rounded-2xl p-2 bg-slate-50 touch-none hover:border-birawa-400 transition-colors">
                            <canvas id="clientCanvas" class="w-full h-40 bg-white rounded-xl shadow-sm"></canvas>
                        </div>
                        <div class="flex justify-end mt-2">
                            <button type="button" id="clearClient" class="text-xs font-bold text-rose-600 hover:text-rose-700 hover:underline">Clear Signature</button>
                        </div>
                        <input type="hidden" name="client_signature" id="clientSignatureInput">
                    </div>

                    <!-- Doctor Signature -->
                    <div class="mb-8">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Doctor Signature</label>
                        <div class="border-2 border-dashed border-slate-300 rounded-2xl p-2 bg-slate-50 touch-none hover:border-birawa-400 transition-colors">
                            <canvas id="doctorCanvas" class="w-full h-40 bg-white rounded-xl shadow-sm"></canvas>
                        </div>
                        <div class="flex justify-end mt-2">
                            <button type="button" id="clearDoctor" class="text-xs font-bold text-rose-600 hover:text-rose-700 hover:underline">Clear Signature</button>
                        </div>
                        <input type="hidden" name="doctor_signature" id="doctorSignatureInput">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-birawa-600 hover:bg-birawa-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all active:scale-95 flex justify-center items-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Sign & Confirm Consent
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Signature Pad JS -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Helper to resize canvas for high DPI
            function resizeCanvas(canvas) {
                var ratio =  Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
            }

            // Client Pad
            var clientCanvas = document.getElementById('clientCanvas');
            var clientPad = new SignaturePad(clientCanvas, { backgroundColor: 'rgb(255, 255, 255)' });
            
            // Doctor Pad
            var doctorCanvas = document.getElementById('doctorCanvas');
            var doctorPad = new SignaturePad(doctorCanvas, { backgroundColor: 'rgb(255, 255, 255)' });

            // Resize on init and window resize
            window.addEventListener("resize", function() {
                resizeCanvas(clientCanvas);
                resizeCanvas(doctorCanvas);
            });
            resizeCanvas(clientCanvas);
            resizeCanvas(doctorCanvas);

            // Clear Buttons
            document.getElementById('clearClient').addEventListener('click', function () {
                clientPad.clear();
            });
            document.getElementById('clearDoctor').addEventListener('click', function () {
                doctorPad.clear();
            });

            // Form Submit
            document.getElementById('consentForm').addEventListener('submit', function (e) {
                e.preventDefault(); // Prevent actual submit for demo purposes
                
                if (clientPad.isEmpty() || doctorPad.isEmpty()) {
                    alert("Both signatures are required.");
                    return;
                }

                document.getElementById('clientSignatureInput').value = clientPad.toDataURL();
                document.getElementById('doctorSignatureInput').value = doctorPad.toDataURL();

                // Here you would normally submit the form
                alert("Signatures captured! Form valid.");
                // this.submit(); 
            });
        });
    </script>
</x-app-layout>
