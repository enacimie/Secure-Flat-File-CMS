<?php

namespace App\Core;

class Shortcode
{
    public static function parse(string $content): string
    {
        // Simple string replacement for now. 
        // Can be expanded with Regex if parameters are needed later like [contact-form to="..."]
        if (strpos($content, '[contact-form]') !== false) {
            $formHtml = self::renderContactForm();
            $content = str_replace('[contact-form]', $formHtml, $content);
        }

        return $content;
    }

    private static function renderContactForm(): string
    {
        $csrf = Security::generateCsrfToken();
        
        return <<<HTML
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 my-8 not-prose">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Contact Us</h3>
            <form action="/contact" method="post" class="space-y-4">
                <input type="hidden" name="csrf" value="$csrf">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Message</label>
                    <textarea name="message" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                </div>
                
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Send Message
                </button>
            </form>
        </div>
HTML;
    }
}
