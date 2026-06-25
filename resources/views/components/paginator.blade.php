@props(['paginator'])

@if ($paginator && $paginator->hasPages())
    <div class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-gray-50/30">
        <div
            class="w-full 
            [&_nav]:flex [&_nav]:flex-col sm:[&_nav]:flex-row [&_nav]:items-center [&_nav]:justify-between [&_nav]:gap-4
            [&_p]:text-xs sm:[&_p]:text-sm [&_p]:text-gray-500 [&_p]:font-light
            
            {{-- Base shapes and layouts for link items --}}
            [&_span]:rounded-xl [&_a]:rounded-xl 
            [&_a]:transition-all [&_a]:duration-200 [&_a]:shadow-sm
            
            {{-- Active State styling --}}
            [&_span[aria-current='page']_span]:bg-[#C85C6E] 
            [&_span[aria-current='page']_span]:border-[#C85C6E] 
            [&_span[aria-current='page']_span]:text-white 
            [&_span[aria-current='page']_span]:shadow-md 
            [&_span[aria-current='page']_span]:shadow-[#C85C6E]/10
            
            {{-- Interactive hover and focus styles --}}
            [&_a:hover]:text-[#C85C6E] 
            [&_a:hover]:border-[#C85C6E] 
            [&_a:hover]:bg-[#C85C6E]/5
            [&_a:focus]:ring-2 
            [&_a:focus]:ring-[#C85C6E]/20 
            [&_a:focus]:outline-none
            
            {{-- Icon scaling --}}
            [&_svg]:w-4 [&_svg]:h-4">

            {{ $paginator->withQueryString()->links() }}
        </div>
    </div>
@endif
