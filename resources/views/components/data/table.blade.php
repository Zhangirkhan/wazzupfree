@props([
    'headers' => [],
    'sortable' => false,
    'currentSort' => null,
    'currentDirection' => 'asc'
])

<div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600 scrollbar-track-gray-100 dark:scrollbar-track-gray-800 w-full">
    <table class="w-full divide-y divide-gray-300 dark:divide-gray-600">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                @foreach($headers as $key => $header)
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        @if($sortable && isset($header['sortable']) && $header['sortable'])
                            <button 
                                class="group inline-flex items-center hover:text-gray-700 dark:hover:text-gray-200"
                                wire:click="sortBy('{{ $key }}')"
                            >
                                {{ $header['label'] ?? $header }}
                                <span class="ml-2 flex-none rounded">
                                    @if($currentSort === $key)
                                        @if($currentDirection === 'asc')
                                            <svg class="h-5 w-5 text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5 text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="h-5 w-5 text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </span>
                            </button>
                        @else
                            {{ $header['label'] ?? $header }}
                        @endif
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            {{ $slot }}
        </tbody>
    </table>
</div>

<style>
    /* Custom scrollbar styles */
    .scrollbar-thin::-webkit-scrollbar {
        height: 6px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Dark mode scrollbar */
    .dark .scrollbar-thin::-webkit-scrollbar-track {
        background: #1e293b;
    }
    
    .dark .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #475569;
    }
    
    .dark .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
</style>
