@include('blob', ['fixed' => true])
<div class=" w-full overflow-hidden bg-gray-900 relative hidden lg:block rounded-l-xl m-8 rounded-r-xl">
    <!-- Gradient overlay -->
    <div class="absolute inset-0 bg-gradient-to-t from-primary-900/50 via-primary-900/50 to-primary-900/0 z-10"></div>
    
    <!-- Green tint overlay -->
    <div class="absolute inset-0 from-primary-600/30 via-primary-400/50 to-primary-700/0 bg-gradient-to-t mix-blend-multiply z-10"></div>
    
    <!-- Aurora background effect -->
    <div class="absolute inset-0 z-0">
        <div class="aurora-bg"></div>
        <div class="absolute inset-0 bg-cover bg-center opacity-40" style="background-image: url('{{  asset('auth-bg.webp') }}')"></div>
    </div>

    <div class="absolute top-16 left-8 w-full z-10">
        <img src="{{ asset('logo-white.svg') }}" alt="Uppi" class="w-32  ">
    </div>
    
    <!-- auth-banner content -->
    <div class="absolute bottom-16 left-8 right-8 flex flex-col gap-3 text-white z-20">
     
        
        <p class="text-2xl md:text-4xl font-semibold leading-tight w-3/4">Be the first to know when your website goes down.
        </p>
       
    </div>
</div> 