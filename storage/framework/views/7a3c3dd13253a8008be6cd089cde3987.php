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

  <?php if(session('success')): ?>
    <div id="success-alert" class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg">
      <?php echo e(session('success')); ?>

    </div>
  <?php endif; ?>

  <main class="flex-1 flex flex-col min-h-screen relative w-full">

    <header
      class="bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl sticky top-0 z-30 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 w-full px-4 lg:px-8 py-3 lg:py-4 shadow-sm font-manrope antialiased tracking-tight">
      <div class="flex items-center gap-3 lg:gap-8 pl-10 lg:pl-0">
        <h1 class="text-lg lg:text-xl font-extrabold tracking-tighter text-blue-900 dark:text-blue-100">Stok Masuk</h1>
      </div>
    </header>

    <div class="p-4 lg:p-8 flex-1 overflow-y-auto no-scrollbar">
        <div class="mb-6 lg:mb-8">
            <?php if (isset($component)) { $__componentOriginalba70b7059b726609ea102a7adde151ac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalba70b7059b726609ea102a7adde151ac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.report-header','data' => ['title' => 'Stock In History','module' => 'Inventory','submodule' => 'Stock In Management','description' => 'Track incoming stock from suppliers and manage inventory receipts.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('report-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Stock In History','module' => 'Inventory','submodule' => 'Stock In Management','description' => 'Track incoming stock from suppliers and manage inventory receipts.']); ?>
                 <?php $__env->slot('actions', null, []); ?> 
                    <a href="/stock-in/create" class="flex items-center px-4 lg:px-5 py-2 lg:py-2.5 bg-primary text-white font-bold rounded-lg shadow-md hover:bg-primary-container active:scale-95 transition-all text-xs lg:text-sm cursor-pointer">
                        <span class="material-symbols-outlined mr-1 lg:mr-2 text-base lg:text-lg">add_circle</span>
                        New Stock In
                    </a>
                 <?php $__env->endSlot(); ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalba70b7059b726609ea102a7adde151ac)): ?>
<?php $attributes = $__attributesOriginalba70b7059b726609ea102a7adde151ac; ?>
<?php unset($__attributesOriginalba70b7059b726609ea102a7adde151ac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalba70b7059b726609ea102a7adde151ac)): ?>
<?php $component = $__componentOriginalba70b7059b726609ea102a7adde151ac; ?>
<?php unset($__componentOriginalba70b7059b726609ea102a7adde151ac); ?>
<?php endif; ?>
        </div>

      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mb-6 lg:mb-8">
        <div class="bg-surface-container-lowest p-4 lg:p-6 rounded-xl shadow-[0_12px_32px_rgba(0,26,64,0.04)] flex flex-col">
          <span class="text-[10px] lg:text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Transactions</span>
          <div class="flex items-baseline gap-2">
            <span class="text-lg lg:text-2xl font-extrabold text-blue-900 font-headline"><?php echo e($stockIns->count()); ?></span>
          </div>
        </div>
        <div class="bg-surface-container-lowest p-4 lg:p-6 rounded-xl shadow-[0_12px_32px_rgba(0,26,64,0.04)] flex flex-col">
          <span class="text-[10px] lg:text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Value</span>
          <div class="flex items-baseline gap-2">
            <span class="text-lg lg:text-2xl font-extrabold text-green-600 font-headline">Rp <?php echo e(number_format($stockIns->sum('total_amount'), 0, ',', '.')); ?></span>
          </div>
        </div>
      </div>

      <div class="bg-surface-container-lowest rounded-xl lg:rounded-2xl shadow-[0_12px_32px_rgba(0,26,64,0.06)] overflow-hidden mb-6">
        <div class="p-4 lg:p-6 bg-surface-container-low/30 border-b border-slate-100">
          <form method="GET" action="/stock-in" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="space-y-1.5">
              <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Date From</label>
              <input name="date_from" value="<?php echo e(request('date_from')); ?>" type="date"
                class="w-full bg-surface-container-low border-none rounded-lg py-2.5 px-4 text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none" />
            </div>
            <div class="space-y-1.5">
              <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Date To</label>
              <input name="date_to" value="<?php echo e(request('date_to')); ?>" type="date"
                class="w-full bg-surface-container-low border-none rounded-lg py-2.5 px-4 text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none" />
            </div>
            <div class="space-y-1.5">
              <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Supplier</label>
              <select name="supplier_id"
                class="w-full bg-surface-container-low border-none rounded-lg py-2.5 px-4 text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                <option value="">All Suppliers</option>
                <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($supplier->id); ?>" <?php echo e(request('supplier_id') == $supplier->id ? 'selected' : ''); ?>><?php echo e($supplier->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="flex items-end">
              <button type="submit" class="w-full bg-primary text-white py-2.5 rounded-lg font-bold text-sm hover:bg-primary-container transition-all cursor-pointer">
                Filter
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="bg-surface-container-lowest rounded-xl lg:rounded-2xl shadow-[0_12px_32px_rgba(0,26,64,0.06)] overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-surface-container-low/50">
                <th class="px-3 lg:px-6 py-3 lg:py-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Reference</th>
                <th class="px-3 lg:px-6 py-3 lg:py-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest hidden md:table-cell">Supplier</th>
                <th class="px-3 lg:px-6 py-3 lg:py-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest hidden lg:table-cell">Date</th>
                <th class="px-3 lg:px-6 py-3 lg:py-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest hidden lg:table-cell">Total Amount</th>
                <th class="px-3 lg:px-6 py-3 lg:py-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
              <?php $__empty_1 = true; $__currentLoopData = $stockIns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stockIn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-blue-50/30 transition-colors group">
                  <td class="px-3 lg:px-6 py-3 lg:py-5">
                    <div class="flex items-center gap-3 lg:gap-4">
                      <div class="w-10 lg:w-12 h-10 lg:h-12 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-green-600 text-lg lg:text-xl">inventory</span>
                      </div>
                      <div>
                        <div class="text-xs lg:text-sm font-bold text-on-surface"><?php echo e($stockIn->reference_no ?? '#' . $stockIn->id); ?></div>
                        <div class="text-[10px] lg:text-[11px] text-slate-400 font-medium"><?php echo e($stockIn->store->name ?? '-'); ?></div>
                      </div>
                    </div>
                  </td>
                  <td class="px-3 lg:px-6 py-3 lg:py-5 hidden md:table-cell">
                    <span class="text-xs lg:text-sm font-medium text-on-surface"><?php echo e($stockIn->supplier->name); ?></span>
                  </td>
                  <td class="px-3 lg:px-6 py-3 lg:py-5 hidden lg:table-cell">
                    <span class="text-xs lg:text-sm font-medium text-on-surface"><?php echo e($stockIn->date->format('d M Y')); ?></span>
                  </td>
                  <td class="px-3 lg:px-6 py-3 lg:py-5 hidden lg:table-cell">
                    <span class="text-xs lg:text-sm font-bold text-green-600">Rp <?php echo e(number_format($stockIn->total_amount, 0, ',', '.')); ?></span>
                  </td>
                  <td class="px-3 lg:px-6 py-3 lg:py-5 text-right">
                    <div class="flex items-center justify-end gap-1">
                      <a href="/stock-in/<?php echo e($stockIn->id); ?>"
                        class="p-1.5 lg:p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors cursor-pointer"
                        title="View Details">
                        <span class="material-symbols-outlined text-base lg:text-sm">visibility</span>
                      </a>
                      <button onclick="confirmDelete(<?php echo e($stockIn->id); ?>)"
                        class="p-1.5 lg:p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer"
                        title="Delete">
                        <span class="material-symbols-outlined text-base lg:text-sm">delete</span>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="5" class="px-6 py-8 text-center text-slate-500">No stock-in transactions found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <form id="deleteStockInForm" method="POST" class="hidden">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
  </form>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    function confirmDelete(id) {
      Swal.fire({
        title: 'Delete Stock In?',
        text: 'Are you sure you want to delete this stock-in transaction?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          const form = document.getElementById('deleteStockInForm');
          form.action = '/stock-in/' + id;
          form.submit();
        }
      });
    }

    setTimeout(() => {
      const successAlert = document.getElementById('success-alert');
      if (successAlert) successAlert.style.display = 'none';
    }, 3000);
  </script>

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
<?php /**PATH H:\project\LARAVEL\POS\POS-app\resources\views/pages/stock-in.blade.php ENDPATH**/ ?>