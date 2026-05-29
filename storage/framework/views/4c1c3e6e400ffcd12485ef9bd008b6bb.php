<?php if (isset($component)) { $__componentOriginal1f9e5f64f242295036c059d9dc1c375c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1f9e5f64f242295036c059d9dc1c375c = $attributes; } ?>
<?php $component = App\View\Components\Layout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Layout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('title', null, []); ?> <?php echo e($title); ?> <?php $__env->endSlot(); ?>

    <main class="flex-1 flex flex-col min-h-screen relative w-full">
        <header class="bg-white/70 backdrop-blur-xl sticky top-0 z-30 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 w-full px-4 lg:px-8 py-3 lg:py-4 shadow-sm">
            <div class="flex items-center gap-3 lg:gap-8 pl-10 lg:pl-0">
                <h1 class="text-lg lg:text-xl font-extrabold tracking-tighter text-blue-900">Backup Database</h1>
            </div>
        </header>

        <div class="p-4 lg:p-8 flex-1 overflow-y-auto no-scrollbar">
            <div class="max-w-5xl mx-auto">

                <?php if(session('success')): ?>
                <div class="bg-green-50 border border-green-200 rounded-2xl p-4 mb-6 flex items-start gap-3">
                    <span class="material-symbols-outlined text-green-500 flex-shrink-0 mt-0.5">check_circle</span>
                    <p class="text-sm font-bold text-green-700"><?php echo e(session('success')); ?></p>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-600 flex-shrink-0">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
                <?php endif; ?>

                <?php if(session('error') || $errors->any()): ?>
                <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6 flex items-start gap-3">
                    <span class="material-symbols-outlined text-red-500 flex-shrink-0 mt-0.5">error</span>
                    <div>
                        <p class="text-sm font-bold text-red-700"><?php echo e(session('error') ?: $errors->first()); ?></p>
                    </div>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600 flex-shrink-0">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
                <?php endif; ?>

                <!-- Info Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                                <span class="material-symbols-outlined text-blue-600">storage</span>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Koneksi</p>
                                <p class="text-sm font-extrabold text-slate-800"><?php echo e($connection); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                                <span class="material-symbols-outlined text-green-600">database</span>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ukuran DB</p>
                                <p class="text-sm font-extrabold text-slate-800"><?php echo e($dbSize ? number_format($dbSize / 1024, 1) . ' KB' : 'N/A'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                                <span class="material-symbols-outlined text-amber-600">folder_zip</span>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Backup</p>
                                <p class="text-sm font-extrabold text-slate-800"><?php echo e($backups->count()); ?> file (<?php echo e(number_format($storageUsed / 1024, 1)); ?> KB)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-800">Buat Backup Baru</h3>
                            <p class="text-xs text-slate-400 mt-1">Membuat salinan database saat ini</p>
                        </div>
                        <form method="POST" action="/backup/create">
                            <?php echo csrf_field(); ?>
                            <button type="submit"
                                class="px-6 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition-all active:scale-95 flex items-center gap-2 shadow-lg shadow-primary/20">
                                <span class="material-symbols-outlined text-base">backup</span>
                                Backup Sekarang
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Restore -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-800">Restore Database</h3>
                            <p class="text-xs text-slate-400 mt-1">Pilih file backup (.sql atau .sqlite) untuk dikembalikan</p>
                        </div>
                        <form method="POST" action="/backup/restore" enctype="multipart/form-data" class="flex items-center gap-3">
                            <?php echo csrf_field(); ?>
                            <input type="file" name="file" accept=".sql,.sqlite,.db"
                                class="text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 transition-all" />
                            <button type="submit"
                                class="px-5 py-2.5 bg-amber-500 text-white font-bold rounded-xl hover:bg-amber-600 transition-all active:scale-95 flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">restore_page</span>
                                Restore
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Backup Files -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-extrabold text-slate-800">Daftar Backup</h3>
                    </div>

                    <?php if($backups->isEmpty()): ?>
                    <div class="p-10 text-center">
                        <span class="material-symbols-outlined text-4xl text-slate-300 mb-3">folder_off</span>
                        <p class="text-sm font-bold text-slate-400">Belum ada backup</p>
                        <p class="text-xs text-slate-300 mt-1">Klik "Backup Sekarang" untuk membuat backup pertama</p>
                    </div>
                    <?php else: ?>
                    <div class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $backups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $backup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="px-5 py-4 flex items-center justify-between gap-4 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-slate-500">description</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-700 truncate"><?php echo e($backup['name']); ?></p>
                                    <p class="text-xs text-slate-400">
                                        <?php echo e(number_format($backup['size'] / 1024, 1)); ?> KB
                                        &middot; <?php echo e(\Carbon\Carbon::createFromTimestamp($backup['date'])->diffForHumans()); ?>

                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <a href="/backup/<?php echo e($backup['name']); ?>/download"
                                    class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition-all">
                                    <span class="material-symbols-outlined text-sm">download</span>
                                </a>
                                <form method="POST" action="/backup/<?php echo e($backup['name']); ?>" onsubmit="return confirm('Hapus backup ini?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit"
                                        class="w-9 h-9 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition-all">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </main>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1f9e5f64f242295036c059d9dc1c375c)): ?>
<?php $attributes = $__attributesOriginal1f9e5f64f242295036c059d9dc1c375c; ?>
<?php unset($__attributesOriginal1f9e5f64f242295036c059d9dc1c375c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1f9e5f64f242295036c059d9dc1c375c)): ?>
<?php $component = $__componentOriginal1f9e5f64f242295036c059d9dc1c375c; ?>
<?php unset($__componentOriginal1f9e5f64f242295036c059d9dc1c375c); ?>
<?php endif; ?>
<?php /**PATH H:\project\LARAVEL\POS\POS-app\resources\views/pages/backup.blade.php ENDPATH**/ ?>