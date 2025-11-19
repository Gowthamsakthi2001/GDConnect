<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>B2B Rider App – Terms & Conditions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #10b981;
            --secondary-color: #059669;
            --accent-color: #34d399;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --border-color: #e5e7eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: var(--text-dark);
            line-height: 1.7;
            padding: 2rem 0;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
        }

        .terms-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .terms-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .terms-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 15s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .terms-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .terms-header p {
            font-size: 1.1rem;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }

        .terms-body {
            padding: 3rem 2.5rem;
        }

        .section {
            margin-bottom: 2.5rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
            animation: fadeIn 0.8s ease-out;
        }

        .section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .section-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent-color), var(--primary-color));
            color: white;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.95rem;
            margin-right: 0.75rem;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .section h4 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .section p {
            color: var(--text-light);
            font-size: 1.05rem;
            margin-bottom: 1rem;
        }

        .section ul {
            list-style: none;
            padding: 0;
        }

        .section ul li {
            position: relative;
            padding-left: 2rem;
            margin-bottom: 0.85rem;
            color: var(--text-light);
            font-size: 1.02rem;
            transition: all 0.3s ease;
        }

        .section ul li:hover {
            color: var(--text-dark);
            transform: translateX(5px);
        }

        .section ul li::before {
            content: '✓';
            position: absolute;
            left: 0;
            top: 0;
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .section ul ul {
            margin-top: 0.75rem;
            margin-left: 1rem;
        }

        .section ul ul li::before {
            content: '→';
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .acceptance-box {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 2px solid #10b981;
            border-radius: 16px;
            padding: 2rem;
            margin-top: 2rem;
            text-align: center;
        }

        .acceptance-box h4 {
            color: #065f46;
            font-size: 1.4rem;
            margin-bottom: 1rem;
            display: block;
        }

        .acceptance-box p {
            color: #047857;
            font-size: 1.05rem;
            margin: 0;
        }

        .highlight-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 4px solid #f59e0b;
            border-radius: 12px;
            padding: 1.25rem;
            margin: 1.5rem 0;
        }

        .highlight-warning p {
            color: #92400e;
            margin: 0;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem 0;
            }

            .terms-header h1 {
                font-size: 1.8rem;
            }

            .terms-header p {
                font-size: 1rem;
            }

            .terms-body {
                padding: 2rem 1.5rem;
            }

            .section h4 {
                font-size: 1.25rem;
            }

            .section-number {
                width: 32px;
                height: 32px;
                font-size: 0.85rem;
            }
        }

        .scroll-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .scroll-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 32px rgba(16, 185, 129, 0.5);
        }

        .scroll-top.show {
            display: flex;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="terms-card">
        <div class="terms-header">
            <h1>📱 B2B Rider App</h1>
            <p>Terms & Conditions</p>
        </div>

        <div class="terms-body">
            <section class="section">
                <h4><span class="section-number">1</span> Introduction</h4>
                <p>These Terms and Conditions ("Terms") govern the use of the B2B Rider App ("App") provided by the Company. By registering and using this App, the Rider agrees to follow all the rules, policies, and guidelines stated below.</p>
            </section>

            <section class="section">
                <h4><span class="section-number">2</span> Rider Eligibility</h4>
                <ul>
                    <li>The Rider must be at least 18 years old.</li>
                    <li>The Rider must possess a valid driving license.</li>
                    <li>The Rider must provide accurate personal and contact details during registration.</li>
                    <li>The Rider must clear the verification process conducted by the Company.</li>
                </ul>
            </section>

            <section class="section">
                <h4><span class="section-number">3</span> Vehicle Assignment</h4>
                <ul>
                    <li>The Company or Partner assigns a vehicle to the Rider based on availability.</li>
                    <li>The Rider must use only the assigned vehicle while performing duties.</li>
                    <li>The Rider must ensure the vehicle is in good condition before starting work.</li>
                    <li>Any damage, accident, or issue with the vehicle must be immediately reported.</li>
                </ul>
            </section>

            <section class="section">
                <h4><span class="section-number">4</span> Rider Responsibilities</h4>
                <ul>
                    <li>Follow all traffic rules and government regulations.</li>
                    <li>Handle the assigned vehicle responsibly and safely.</li>
                    <li>Pick up and deliver items as per the instructions in the App.</li>
                    <li>Maintain professionalism and polite communication with customers/clients.</li>
                    <li>Keep the App active during working hours.</li>
                    <li>Avoid any fraudulent activities, misuse of the App, or unacceptable behavior.</li>
                </ul>
            </section>

            <section class="section">
                <h4><span class="section-number">5</span> Delivery & Task Handling</h4>
                <ul>
                    <li>The Rider must verify the order/task details before starting.</li>
                    <li>The Rider must ensure safe delivery of goods without damage.</li>
                    <li>The Rider should follow the optimized route suggested by the App unless otherwise required.</li>
                    <li>The Rider must upload proof of delivery when required (signature, OTP, photo, etc.)</li>
                </ul>
            </section>

            <section class="section">
                <h4><span class="section-number">6</span> Earnings & Payment</h4>
                <ul>
                    <li>Payments to the Rider will be processed as per the payout schedule.</li>
                    <li>The Company may deduct penalties for rule violations, theft, fraud, or vehicle damage.</li>
                    <li>Incentives, bonuses, and extra earnings (if applicable) are based on performance.</li>
                </ul>
            </section>

            <section class="section">
                <h4><span class="section-number">7</span> Vehicle Care & Maintenance</h4>
                <ul>
                    <li>The Rider must maintain cleanliness and proper handling of the assigned vehicle.</li>
                    <li>Fuel usage, maintenance costs, and other conditions will be as per the Company's policy.</li>
                    <li>Riders should not allow any unauthorized person to drive the assigned vehicle.</li>
                </ul>
            </section>

            <section class="section">
                <h4><span class="section-number">8</span> Safety Policy</h4>
                <div class="highlight-warning">
                    <p><strong>⚠️ Safety First:</strong> Your safety is our priority. Please follow all safety guidelines.</p>
                </div>
                <ul>
                    <li>Wearing a helmet/seatbelt is mandatory.</li>
                    <li>The Rider must avoid reckless driving, over-speeding, or driving under the influence.</li>
                    <li>The Company is not responsible for personal injuries caused due to Rider's negligence.</li>
                </ul>
            </section>

            <section class="section">
                <h4><span class="section-number">9</span> App Usage Rules</h4>
                <ul>
                    <li>The Rider must not share login details with anyone.</li>
                    <li>The App should be used only for official work.</li>
                    <li>Tampering, hacking, or modifying the App is strictly prohibited.</li>
                </ul>
            </section>

            <section class="section">
                <h4><span class="section-number">10</span> Suspension & Termination</h4>
                <p>The Company reserves the right to suspend or terminate the Rider's access if:</p>
                <ul>
                    <li>There is violation of any Terms.</li>
                    <li>The Rider receives multiple customer complaints.</li>
                    <li>Fraudulent activities or misuse of the vehicle/App are detected.</li>
                    <li>Repeated delays, negligence, or irresponsible behavior is observed.</li>
                </ul>
            </section>

            <section class="section">
                <h4><span class="section-number">11</span> Data & Privacy</h4>
                <ul>
                    <li>The Rider's personal data will be collected only for verification and work-related purposes.</li>
                    <li>The Company will not share Rider data with third parties except as required by law.</li>
                </ul>
            </section>

            <section class="section">
                <h4><span class="section-number">12</span> Liability</h4>
                <ul>
                    <li>The Company is not responsible for delays caused by traffic, weather, or unforeseen events.</li>
                    <li>The Rider is responsible for safe handling of goods and assigned vehicle.</li>
                    <li>Any loss or damage caused due to Rider negligence will be recovered from the Rider.</li>
                </ul>
            </section>

            <div class="acceptance-box">
                <h4>✅ Acceptance</h4>
                <p>By logging in and using the B2B Rider App, the Rider confirms that they have read, understood, and agreed to these Terms & Conditions.</p>
            </div>
        </div>
    </div>
</div>

<button class="scroll-top" id="scrollTop">↑</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const scrollTopBtn = document.getElementById('scrollTop');
    
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            scrollTopBtn.classList.add('show');
        } else {
            scrollTopBtn.classList.remove('show');
        }
    });
    
    scrollTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
</script>
</body>
</html>
