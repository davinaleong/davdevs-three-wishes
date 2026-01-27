<x-admin-layout>
    <x-slot name="title">Email Management</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Email Tools</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Broadcast Email -->
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-blue-900 mb-4">Broadcast Email</h3>
                            <p class="text-blue-700 mb-4">Send email to all verified users</p>
                            <form action="{{ route('admin.emails.broadcast') }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="subject" class="block text-sm font-medium text-blue-900">Subject</label>
                                    <input type="text" name="subject" id="subject" required class="mt-1 block w-full rounded-md border-gray-300">
                                </div>
                                <div class="mb-4">
                                    <label for="message" class="block text-sm font-medium text-blue-900">Message</label>
                                    <textarea name="message" id="message" rows="4" required class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                                </div>
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Send Broadcast</button>
                            </form>
                        </div>

                        <!-- Year-End Batch -->
                        <div class="bg-green-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-green-900 mb-4">Year-End Reminders</h3>
                            <p class="text-green-700 mb-4">Send wish reminders for the active theme year</p>
                            <form action="{{ route('admin.emails.year-end-batch') }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Send Year-End Reminders</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>