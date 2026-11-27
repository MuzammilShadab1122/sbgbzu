<?php
// virtual_card_preview.php - 2.5" x 4" Virtual Attendee Card Preview for AWS Student Community Day Multan 2026
$pageTitle = 'Virtual Attendee Card (2.5" x 4" with QR Code) | AWS SBG BZU';
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="public/js/qrcode.min.js"></script>
    <script src="public/js/html2canvas.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        space: ['Space Grotesk', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #080313;
            color: #f8fafc;
            min-height: 100vh;
        }
        h1, h2, h3, h4, h5, h6, .font-space {
            font-family: 'Space Grotesk', sans-serif;
        }
        
        /* Exact 2.5" x 4" Card Proportions (Ratio 2.5:4 = 5:8) */
        .card-25x4 {
            width: 350px;
            height: 560px;
            aspect-ratio: 2.5 / 4;
            background: radial-gradient(130% 130% at 0% 0%, rgba(168, 85, 247, 0.35) 0%, transparent 50%),
                        radial-gradient(100% 100% at 100% 100%, rgba(245, 158, 11, 0.22) 0%, transparent 50%),
                        linear-gradient(145deg, #120527 0%, #1c0838 35%, #140529 70%, #0a0215 100%);
            border: 1.5px solid rgba(168, 85, 247, 0.45);
            box-shadow: 0 25px 60px -15px rgba(109, 40, 217, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }
        .card-25x4::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: linear-gradient(to right, rgba(255, 255, 255, 0.035) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(255, 255, 255, 0.035) 1px, transparent 1px);
            background-size: 20px 20px;
            pointer-events: none;
            z-index: 1;
        }
        .card-25x4::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                60deg,
                transparent 30%,
                rgba(255, 255, 255, 0.04) 45%,
                rgba(245, 158, 11, 0.09) 50%,
                rgba(168, 85, 247, 0.09) 55%,
                transparent 70%
            );
            pointer-events: none;
            z-index: 2;
        }
        .text-glow-purple {
            background: linear-gradient(135deg, #ffffff 0%, #fae8ff 40%, #d8b4fe 80%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="py-8 px-4 sm:px-6 lg:px-8 selection:bg-purple-600 selection:text-white">

    <div class="max-w-5xl mx-auto">

        <!-- Top Header Controls -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-purple-500/20 pb-5">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/15 border border-purple-500/30 text-purple-300 text-[10px] font-black uppercase tracking-widest mb-1.5">
                    <span class="h-2 w-2 rounded-full bg-amber-400 animate-pulse"></span>
                    2.5" &times; 4" Attendee Card Template with QR Code
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white font-space tracking-tight">
                    AWS Student Community Day Multan 2026 Pass
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">
                    Dimensions: <strong>2.5 inch width &times; 4 inch length</strong> (Phone-friendly badge). Includes dynamic Meetup QR code.
                </p>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="index.php" class="px-3.5 py-2 rounded-xl border border-white/10 hover:bg-white/5 text-slate-300 text-xs font-bold transition-all">
                    &larr; Back to Home
                </a>
                <button onclick="downloadCardPNG()" id="btn-download" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 via-fuchsia-600 to-amber-500 hover:opacity-95 text-white text-xs font-black uppercase tracking-wider shadow-lg shadow-purple-600/30 flex items-center gap-2 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Download Card (.PNG)</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- LEFT: 2.5" x 4" Card Visual Preview -->
            <div class="lg:col-span-6 flex flex-col items-center">
                
                <!-- Dimension Indicator -->
                <div class="mb-3 flex items-center justify-between w-full max-w-[350px] text-[11px] font-mono text-purple-300 font-bold px-1">
                    <span>Width: 2.5 inches (350px)</span>
                    <span>Length: 4.0 inches (560px)</span>
                </div>

                <!-- THE CARD (350px x 560px = 2.5" x 4") -->
                <div id="card-badge" class="card-25x4 rounded-[28px] p-5 flex flex-col justify-between text-white select-none shadow-2xl">
                    
                    <!-- Lanyard Hole Notch Aesthetic -->
                    <div class="absolute top-2 left-1/2 -translate-x-1/2 w-14 h-1.5 rounded-full bg-black/40 border border-white/10 z-20"></div>

                    <!-- 1. Top Header: SBG Logo Left, AWS Logo Right -->
                    <div class="relative z-10 flex items-center justify-between gap-2 pt-2 border-b border-purple-400/20 pb-3">
                        <!-- Top Left: AWS SBG BZU -->
                        <div class="flex items-center gap-2">
                            <div class="relative h-10 w-10 rounded-full ring-2 ring-purple-400/60 p-0.5 bg-black/50 overflow-hidden shadow shrink-0">
                                <img src="public/images/sbg-logo.png" alt="AWS SBG BZU" class="h-full w-full object-cover rounded-full">
                            </div>
                            <div class="leading-tight">
                                <span class="block text-[9px] font-black uppercase tracking-[0.2em] text-purple-300">
                                    AWS SBG BZU
                                </span>
                                <span class="block text-[7.5px] font-bold uppercase tracking-wider text-slate-400">
                                    Multan Campus
                                </span>
                            </div>
                        </div>

                        <!-- Top Right: AWS Logo & Pass Badge -->
                        <div class="flex items-center gap-1.5">
                            <span class="px-2 py-0.5 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[8px] font-black uppercase tracking-wider">
                                PASS
                            </span>
                            <div class="h-8 w-11 flex items-center justify-center p-1 rounded-lg bg-white/5 border border-white/10">
                                <img src="public/images/stickers/aws.png" alt="AWS" class="h-full w-full object-contain">
                            </div>
                        </div>
                    </div>

                    <!-- 2. Statement Banner: "I am attending AWS Community Day" -->
                    <div class="relative z-10 text-center my-1.5">
                        <div class="inline-block px-3 py-0.5 rounded-full bg-gradient-to-r from-amber-500/25 to-purple-500/25 border border-amber-400/35 text-[9px] font-black uppercase tracking-[0.25em] text-amber-300 mb-1">
                            I AM ATTENDING
                        </div>
                        <h2 class="text-xl font-black font-space tracking-tight leading-tight text-transparent bg-clip-text bg-gradient-to-r from-white via-purple-100 to-amber-200">
                            AWS Student Community Day
                        </h2>
                        <div class="flex items-center justify-center gap-1.5 text-[9px] font-bold text-purple-200 mt-0.5">
                            <span class="text-amber-400 font-black uppercase">Multan 2026</span>
                            <span>&bull;</span>
                            <span>15 Dec 2026</span>
                            <span>&bull;</span>
                            <span>BZU Multan</span>
                        </div>
                    </div>

                    <!-- 3. Attendee Photo & Name -->
                    <div class="relative z-10 flex flex-col items-center my-1">
                        <div class="relative group">
                            <div class="absolute -inset-1 rounded-full bg-gradient-to-r from-purple-500 via-fuchsia-500 to-amber-500 blur-sm opacity-80"></div>
                            <img id="card-photo-img" src="public/images/AWS-MembersPics/Muzammil Shahdab.jpg" alt="Attendee" class="relative h-20 w-20 rounded-full object-cover border-2 border-white/40 shadow-xl bg-slate-900">
                            <div class="absolute bottom-0 right-0 bg-emerald-500 text-white rounded-full p-1 border-2 border-slate-950 shadow">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <h3 id="card-name-display" class="text-base font-black text-white font-space tracking-tight mt-1.5 truncate max-w-[280px]">
                            Muzammil Shadab
                        </h3>
                        <span id="card-role-display" class="text-[9.5px] font-bold text-amber-300 tracking-wide block">
                            Cloud Builder &bull; Official Attendee
                        </span>
                    </div>

                    <!-- 4. Meetup Ticket QR Code Section -->
                    <div class="relative z-10 bg-purple-950/60 p-2.5 rounded-2xl border border-purple-500/30 backdrop-blur-md flex items-center gap-3">
                        <!-- QR Code Container -->
                        <div class="h-20 w-20 rounded-xl bg-white p-1.5 shadow-md flex items-center justify-center shrink-0">
                            <div id="qrcode-box" class="h-full w-full flex items-center justify-center"></div>
                        </div>
                        
                        <!-- Ticket Info Beside QR Code -->
                        <div class="min-w-0 flex-1 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-[8px] font-black uppercase tracking-wider text-purple-300">Meetup Ticket</span>
                                <span class="text-[7.5px] font-black uppercase px-1.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Verified</span>
                            </div>
                            <div id="card-ticket-display" class="text-[12px] font-black font-space text-amber-400 font-mono tracking-wider truncate">
                                #MTUP-BZU-2026-0582
                            </div>
                            <p class="text-[8px] text-slate-300 leading-tight">
                                Scan QR code to verify RSVP on Meetup.
                            </p>
                        </div>
                    </div>

                    <!-- 5. Card Footer -->
                    <div class="relative z-10 pt-2 border-t border-purple-400/20 flex items-center justify-between text-[7.5px] font-bold text-slate-400 uppercase tracking-widest">
                        <span>AWS Student Builder Group</span>
                        <span class="text-purple-300 font-mono">2.5" &times; 4" Pass</span>
                    </div>

                </div>

                <!-- Phone Friendly Note -->
                <p class="text-[11px] text-slate-400 text-center mt-3 max-w-[350px]">
                    📱 <strong>Phone Perfect:</strong> Scaled to 2.5" &times; 4" (5:8 aspect ratio) for flawless vertical display on mobile screens and instant WhatsApp/LinkedIn sharing.
                </p>

            </div>

            <!-- RIGHT: Customizer Controls -->
            <div class="lg:col-span-6 space-y-5">
                
                <div class="p-6 rounded-3xl bg-slate-900/90 border border-purple-500/30 backdrop-blur-xl shadow-xl space-y-5">
                    <div class="border-b border-white/10 pb-4">
                        <h3 class="text-lg font-black text-white font-space">Customize Attendee Card</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Test real-time data binding. The QR code dynamically regenerates for each ticket URL.</p>
                    </div>

                    <!-- Input 1: Attendee Name -->
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-300 mb-1.5">
                            Attendee Name
                        </label>
                        <input type="text" id="input-name" value="Muzammil Shadab" oninput="updateCard()" class="w-full rounded-xl border border-white/15 bg-white/5 px-3.5 py-2.5 text-xs text-white outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/20 transition-all font-semibold">
                    </div>

                    <!-- Input 2: Meetup Ticket ID -->
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-300 mb-1.5">
                            Meetup Ticket ID / Number
                        </label>
                        <input type="text" id="input-ticket" value="#MTUP-BZU-2026-0582" oninput="updateCard()" class="w-full rounded-xl border border-white/15 bg-white/5 px-3.5 py-2.5 text-xs text-amber-300 font-mono outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/20 transition-all font-semibold">
                    </div>

                    <!-- Input 3: Meetup QR Target URL -->
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-300 mb-1.5">
                            Meetup Event URL (Encoded in QR Code)
                        </label>
                        <input type="text" id="input-qr-url" value="https://meetu.ps/e/QljqK/1fyQWj/i" oninput="generateQRCode()" class="w-full rounded-xl border border-white/15 bg-white/5 px-3.5 py-2.5 text-xs text-purple-300 font-mono outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/20 transition-all font-semibold">
                    </div>

                    <!-- Input 4: Role / Designation -->
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-300 mb-1.5">
                            Role / Subtitle
                        </label>
                        <input type="text" id="input-role" value="Cloud Builder • Official Attendee" oninput="updateCard()" class="w-full rounded-xl border border-white/15 bg-white/5 px-3.5 py-2.5 text-xs text-white outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/20 transition-all font-semibold">
                    </div>

                    <!-- Input 5: Select Photo -->
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-300 mb-1.5">
                            Select Sample Photo or Upload
                        </label>
                        <div class="grid grid-cols-4 gap-2 mb-3">
                            <button type="button" onclick="selectPhoto('public/images/AWS-MembersPics/Muzammil Shahdab.jpg')" class="h-12 rounded-xl overflow-hidden border-2 border-purple-500 hover:scale-105 transition-all p-0.5 bg-black/40">
                                <img src="public/images/AWS-MembersPics/Muzammil Shahdab.jpg" class="h-full w-full object-cover rounded-lg">
                            </button>
                            <button type="button" onclick="selectPhoto('public/images/AWS-MembersPics/Mehdi Hassan.jpeg')" class="h-12 rounded-xl overflow-hidden border-2 border-white/20 hover:border-purple-500 hover:scale-105 transition-all p-0.5 bg-black/40">
                                <img src="public/images/AWS-MembersPics/Mehdi Hassan.jpeg" class="h-full w-full object-cover rounded-lg">
                            </button>
                            <button type="button" onclick="selectPhoto('public/images/AWS-MembersPics/Ali-Hassan.jpeg')" class="h-12 rounded-xl overflow-hidden border-2 border-white/20 hover:border-purple-500 hover:scale-105 transition-all p-0.5 bg-black/40">
                                <img src="public/images/AWS-MembersPics/Ali-Hassan.jpeg" class="h-full w-full object-cover rounded-lg">
                            </button>
                            <button type="button" onclick="selectPhoto('public/images/AWS-MembersPics/Hania Amjad.png')" class="h-12 rounded-xl overflow-hidden border-2 border-white/20 hover:border-purple-500 hover:scale-105 transition-all p-0.5 bg-black/40">
                                <img src="public/images/AWS-MembersPics/Hania Amjad.png" class="h-full w-full object-cover rounded-lg">
                            </button>
                        </div>
                        <input type="file" id="input-upload-photo" accept="image/*" onchange="uploadCustomPhoto(event)" class="w-full text-[11px] text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-purple-600 file:text-white hover:file:bg-purple-500 cursor-pointer">
                    </div>

                    <!-- Download Button -->
                    <div class="pt-4 border-t border-white/10">
                        <button onclick="downloadCardPNG()" class="w-full py-4 rounded-2xl bg-gradient-to-r from-purple-600 via-fuchsia-600 to-amber-500 hover:opacity-95 text-white font-black text-xs uppercase tracking-widest shadow-xl shadow-purple-600/30 transition-all cursor-pointer flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Download 2.5" &times; 4" Card (.PNG)</span>
                        </button>
                    </div>

                </div>

                <!-- Verified Specs Checklist -->
                <div class="p-5 rounded-2xl bg-white/[0.02] border border-white/10 space-y-2 text-xs text-slate-300">
                    <h4 class="font-black uppercase tracking-wider text-purple-300 text-[11px]">Updated Specifications:</h4>
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-400 font-bold">&check;</span>
                        <span><strong>Dimensions:</strong> Exactly 2.5" width &times; 4.0" length (5:8 ratio).</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-400 font-bold">&check;</span>
                        <span><strong>Ticket Element:</strong> Real, scannable QR Code linked to Meetup ticket.</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-400 font-bold">&check;</span>
                        <span><strong>File Format:</strong> Downloadable as crisp, high-resolution `.png` file.</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-400 font-bold">&check;</span>
                        <span><strong>Logos & Theme:</strong> Purple theme, AWS SBG BZU logo (top-left), AWS logo (top-right).</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-400 font-bold">&check;</span>
                        <span><strong>Statement:</strong> "I am attending AWS Student Community Day".</span>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Script to Generate Dynamic QR Code and Download PNG -->
    <script>
        let qrcode = null;

        function generateQRCode() {
            const qrContainer = document.getElementById('qrcode-box');
            if (!qrContainer) return;
            qrContainer.innerHTML = '';

            const qrUrl = (document.getElementById('input-qr-url') ? document.getElementById('input-qr-url').value.trim() : '') || 'https://meetu.ps/e/QljqK/1fyQWj/i';
            const ticket = (document.getElementById('input-ticket') ? document.getElementById('input-ticket').value.trim() : '') || '#MTUP-BZU-2026-0582';

            const payload = qrUrl + (qrUrl.includes('?') ? '&' : '?') + 'ticket=' + encodeURIComponent(ticket);

            qrcode = new QRCode(qrContainer, {
                text: payload,
                width: 68,
                height: 68,
                colorDark: '#0e041d',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
        }

        function updateCard() {
            const name = document.getElementById('input-name').value || 'Muzammil Shadab';
            const ticket = document.getElementById('input-ticket').value || '#MTUP-BZU-2026-0582';
            const role = document.getElementById('input-role').value || 'Cloud Builder • Official Attendee';

            document.getElementById('card-name-display').innerText = name;
            document.getElementById('card-ticket-display').innerText = ticket;
            document.getElementById('card-role-display').innerText = role;

            generateQRCode();
        }

        function selectPhoto(src) {
            document.getElementById('card-photo-img').src = src;
        }

        function uploadCustomPhoto(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    selectPhoto(evt.target.result);
                };
                reader.readAsDataURL(file);
            }
        }

        function downloadCardPNG() {
            const cardEl = document.getElementById('card-badge');
            const name = (document.getElementById('input-name').value || 'Attendee').trim().replace(/\s+/g, '_');
            const btn = document.getElementById('btn-download');

            const origHtml = btn.innerHTML;
            btn.innerHTML = '<span>Generating 2.5" &times; 4" PNG...</span>';

            // Scale factor 3x produces 1050px x 1680px (Crisp 420 DPI print / HD mobile view)
            html2canvas(cardEl, {
                scale: 3,
                backgroundColor: null,
                useCORS: true,
                logging: false
            }).then(canvas => {
                btn.innerHTML = origHtml;
                const link = document.createElement('a');
                link.download = `AWS_Community_Day_Pass_${name}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            }).catch(err => {
                console.error(err);
                btn.innerHTML = origHtml;
                alert('Export failed: ' + err.message);
            });
        }

        // Initialize QR code on page load
        document.addEventListener('DOMContentLoaded', function() {
            generateQRCode();
        });
    </script>
</body>
</html>
