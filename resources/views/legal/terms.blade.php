<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Terms and Conditions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="prose prose-lg max-w-none dark:prose-invert">
                        <h1 class="text-3xl font-bold mb-6">Terms and Conditions</h1>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-8">
                            <strong>Effective Date:</strong> January 23, 2026<br>
                            <strong>Last Updated:</strong> January 23, 2026
                        </p>

                        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-6 mb-8">
                            <h2 class="text-xl font-semibold mb-3 text-blue-800 dark:text-blue-200">Welcome to Dav/Devs Three Wishes</h2>
                            <p class="text-blue-700 dark:text-blue-300">
                                Dav/Devs Three Wishes is a faith-based platform designed to help believers set meaningful spiritual intentions and reflect on God's faithfulness. By using our service, you agree to these terms.
                            </p>
                        </div>

                        <h2 class="text-2xl font-semibold mt-8 mb-4">1. Acceptance of Terms</h2>
                        <p>By accessing or using Dav/Devs Three Wishes (the "Service"), you agree to be bound by these Terms and Conditions ("Terms"). If you disagree with any part of these terms, you may not access the Service.</p>

                        <h2 class="text-2xl font-semibold mt-8 mb-4">2. Description of Service</h2>
                        <p>Dav/Devs Three Wishes is a Christian-focused platform that allows users to:</p>
                        <ul class="list-disc list-inside ml-4 mb-4">
                            <li>Set three meaningful spiritual intentions for each year</li>
                            <li>Receive annual reflection emails on December 31st</li>
                            <li>Join a community of believers in prayer and spiritual growth</li>
                            <li>Track their spiritual journey over time</li>
                        </ul>

                        <h2 class="text-2xl font-semibold mt-8 mb-4">3. User Accounts and Registration</h2>
                        <h3 class="text-lg font-semibold mt-6 mb-3">3.1 Account Creation</h3>
                        <p>To use certain features of our Service, you must register for an account. You agree to provide accurate, current, and complete information during registration.</p>
                        
                        <h3 class="text-lg font-semibold mt-6 mb-3">3.2 Account Security</h3>
                        <p>You are responsible for safeguarding your account credentials and for all activities that occur under your account. You must notify us immediately of any unauthorized use.</p>

                        <h3 class="text-lg font-semibold mt-6 mb-3">3.3 Email Verification</h3>
                        <p>A valid email address is required for account activation and to receive our annual reflection emails.</p>

                        <h2 class="text-2xl font-semibold mt-8 mb-4">4. User Content and Conduct</h2>
                        <h3 class="text-lg font-semibold mt-6 mb-3">4.1 Your Three Wishes</h3>
                        <p>You retain ownership of the spiritual intentions ("wishes") you create on our platform. By sharing them with us, you grant us permission to store and process them to provide our services.</p>
                        
                        <h3 class="text-lg font-semibold mt-6 mb-3">4.2 Acceptable Use</h3>
                        <p>You agree to use our Service in a manner consistent with Christian values and our mission. You may not:</p>
                        <ul class="list-disc list-inside ml-4 mb-4">
                            <li>Use the Service for any illegal or harmful purposes</li>
                            <li>Share content that is offensive, hateful, or contrary to Christian teachings</li>
                            <li>Attempt to harm or exploit other users</li>
                            <li>Interfere with the proper functioning of the Service</li>
                        </ul>

                        <h2 class="text-2xl font-semibold mt-8 mb-4">5. Privacy and Data Protection</h2>
                        <p>Your privacy is important to us. Our collection and use of personal information is governed by our <a href="{{ route('legal.privacy') }}" class="text-blue-600 dark:text-blue-400 underline">Privacy Policy</a>, which is incorporated into these Terms by reference.</p>

                        <h2 class="text-2xl font-semibold mt-8 mb-4">6. Communications from Us</h2>
                        <h3 class="text-lg font-semibold mt-6 mb-3">6.1 Annual Reflection Emails</h3>
                        <p>By using our Service, you consent to receive our annual reflection emails on December 31st, containing your year's wishes and spiritual encouragement.</p>
                        
                        <h3 class="text-lg font-semibold mt-6 mb-3">6.2 Service Communications</h3>
                        <p>We may send you service-related emails, including account verification, password resets, and important updates about our Service.</p>

                        <h2 class="text-2xl font-semibold mt-8 mb-4">7. Intellectual Property</h2>
                        <h3 class="text-lg font-semibold mt-6 mb-3">7.1 Our Content</h3>
                        <p>The Service, including its design, functionality, and content (excluding your personal wishes), is owned by Dav/Devs and protected by copyright and other intellectual property laws.</p>
                        
                        <h3 class="text-lg font-semibold mt-6 mb-3">7.2 Scripture and Religious Content</h3>
                        <p>Bible verses and religious content are used for educational and spiritual purposes. We respect the sacred nature of these texts.</p>

                        <h2 class="text-2xl font-semibold mt-8 mb-4">8. Service Availability and Modifications</h2>
                        <h3 class="text-lg font-semibold mt-6 mb-3">8.1 Service Availability</h3>
                        <p>We strive to maintain consistent service availability but cannot guarantee uninterrupted access. We may need to perform maintenance or updates.</p>
                        
                        <h3 class="text-lg font-semibold mt-6 mb-3">8.2 Changes to Service</h3>
                        <p>We may modify or discontinue features of our Service with reasonable notice. We will always aim to preserve your data and spiritual journey.</p>

                        <h2 class="text-2xl font-semibold mt-8 mb-4">9. Limitation of Liability</h2>
                        <p>While we provide this Service as a tool for spiritual growth, we are not responsible for spiritual outcomes or personal decisions. Our liability is limited to the maximum extent permitted by law.</p>

                        <h2 class="text-2xl font-semibold mt-8 mb-4">10. Termination</h2>
                        <h3 class="text-lg font-semibold mt-6 mb-3">10.1 Account Termination</h3>
                        <p>You may terminate your account at any time by contacting us. Upon termination, your data will be handled according to our Privacy Policy.</p>
                        
                        <h3 class="text-lg font-semibold mt-6 mb-3">10.2 Our Right to Terminate</h3>
                        <p>We may terminate accounts for violations of these Terms, but will do so prayerfully and with grace whenever possible.</p>

                        <h2 class="text-2xl font-semibold mt-8 mb-4">11. Governing Law and Dispute Resolution</h2>
                        <p>These Terms are governed by the laws of the jurisdiction where Dav/Devs operates. We encourage resolving disputes through Christian mediation before pursuing legal action.</p>

                        <h2 class="text-2xl font-semibold mt-8 mb-4">12. Changes to Terms</h2>
                        <p>We may update these Terms from time to time. We will notify users of significant changes via email or through our Service. Continued use constitutes acceptance of revised Terms.</p>

                        <h2 class="text-2xl font-semibold mt-8 mb-4">13. Contact Information</h2>
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg mb-8">
                            <h3 class="text-lg font-semibold mb-3">Dav/Devs Three Wishes Ministry Team</h3>
                            <p><strong>Email:</strong> support@gracesoft.dev</p>
                            <p><strong>Data Protection Officer:</strong> privacy@gracesoft.dev</p>
                            <p><strong>Website:</strong> <a href="{{ config('app.url') }}" class="text-blue-600 dark:text-blue-400">{{ config('app.url') }}</a></p>
                        </div>

                        <h2 class="text-2xl font-semibold mt-8 mb-4">14. Spiritual Disclaimer</h2>
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-6 mb-8">
                            <p class="text-yellow-800 dark:text-yellow-200">
                                <strong>Important:</strong> Dav/Devs Three Wishes is a tool for spiritual reflection and goal-setting. It is not a substitute for personal prayer, Bible study, pastoral counsel, or direct relationship with God. We encourage all users to seek spiritual guidance from qualified Christian leaders and to root their faith in Scripture.
                            </p>
                        </div>

                        <div class="border-t pt-8 mt-12 text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                "Trust in the Lord with all your heart and lean not on your own understanding; in all your ways submit to him, and he will make your paths straight." - Proverbs 3:5-6
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-4">
                                By using Dav/Devs Three Wishes, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>