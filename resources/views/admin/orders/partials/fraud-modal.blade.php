<!-- BD Courier Fraud Checker Modal -->
<div id="bdCourierFraudModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="fraud-modal-title" role="dialog" aria-modal="true">
  <!-- Backdrop -->
  <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeFraudModal()"></div>

  <div class="min-h-full flex items-center justify-center p-3 sm:p-4 text-center">
    <div class="relative w-full max-w-3xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-slate-100 my-8">
      
      <!-- Top Accent Bar -->
      <div id="fraudModalBar" class="h-1.5 bg-gradient-to-r from-brand to-brandDark"></div>

      <!-- Modal Header -->
      <div class="p-5 sm:p-6 border-b border-slate-100 bg-slate-50/50 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
          <div id="fraudModalIconContainer" class="h-12 w-12 rounded-xl bg-brand/10 text-brand flex items-center justify-center shrink-0 shadow-xs">
            <i id="fraudModalIcon" class="fa-solid fa-shield-halved text-xl"></i>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <h3 class="font-display font-bold text-slate-900 text-lg sm:text-xl" id="fraud-modal-title">
                কুরিয়ার ডেলিভারি ও ফ্রড রিপোর্ট
              </h3>
              <span id="fraudModalVerdictBadge" class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                লোড হচ্ছে...
              </span>
            </div>
            <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-500">
              <span class="font-mono font-semibold text-slate-800 text-sm" id="fraudModalPhone">01XXXXXXXXX</span>
              <span id="fraudModalCustomerName" class="text-slate-400"></span>
              <button type="button" onclick="copyFraudPhone()" class="text-slate-400 hover:text-brand transition" title="নম্বর কপি করুন">
                <i class="fa-regular fa-copy text-xs"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <button type="button" id="fraudRefreshBtn" onclick="refreshFraudModalData()" title="নতুন করে চেক করুন"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg transition shadow-xs">
            <i class="fa-solid fa-arrows-rotate text-[11px]"></i>
            <span>রিফ্রেশ</span>
          </button>
          <button type="button" onclick="closeFraudModal()" class="text-slate-400 hover:text-slate-700 p-2 rounded-lg hover:bg-slate-100 transition">
            <i class="fa-solid fa-xmark text-lg"></i>
          </button>
        </div>
      </div>

      <!-- Modal Body -->
      <div class="p-5 sm:p-6 space-y-6 max-h-[75vh] overflow-y-auto">
        
        <!-- Loading State -->
        <div id="fraudModalLoading" class="py-12 text-center">
          <div class="inline-block animate-spin text-brand text-3xl mb-3">
            <i class="fa-solid fa-circle-notch"></i>
          </div>
          <p class="text-sm font-semibold text-slate-700">BD Courier থেকে তথ্য যাচাই করা হচ্ছে...</p>
          <p class="text-xs text-slate-400 mt-1">Pathao, SteadFast, RedX, PaperFly ইত্যাদি কুরিয়ার সার্চ হচ্ছে</p>
        </div>

        <!-- Error State -->
        <div id="fraudModalError" class="hidden p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-start gap-3">
          <i class="fa-solid fa-circle-exclamation text-lg shrink-0 mt-0.5"></i>
          <div>
            <p class="font-semibold" id="fraudModalErrorMessage">তথ্য আনতে সমস্যা হয়েছে।</p>
            <p class="text-xs text-red-600 mt-1">অনুগ্রহ করে ইন্টারনেট সংযোগ বা API Key যাচাই করুন এবং পুনরায় চেষ্টা করুন।</p>
          </div>
        </div>

        <!-- Content Section (Shown when loaded) -->
        <div id="fraudModalContent" class="hidden space-y-6">

          <!-- Risk / Action Banner -->
          <div id="fraudVerdictBanner" class="rounded-xl p-4 border flex items-start gap-3 bg-slate-50 border-slate-200 text-slate-800">
            <i id="fraudVerdictBannerIcon" class="fa-solid fa-circle-info text-lg shrink-0 mt-0.5 text-slate-500"></i>
            <div class="flex-1">
              <div class="flex items-center justify-between gap-2">
                <p class="text-sm font-bold" id="fraudVerdictAction">পরামর্শ ও পর্যবেক্ষণ</p>
                <span class="text-[11px] text-slate-400" id="fraudCheckedAt"></span>
              </div>
              <ul id="fraudVerdictReasons" class="mt-1.5 text-xs space-y-0.5 text-slate-600 list-disc list-inside">
              </ul>
            </div>
          </div>

          <!-- Overall Summary Cards (4 Columns) -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100 text-center">
              <div class="h-8 w-8 rounded-lg bg-blue-100 text-blue-600 mx-auto flex items-center justify-center mb-1.5">
                <i class="fa-solid fa-boxes-stacked text-sm"></i>
              </div>
              <p class="text-2xl font-bold text-slate-800 font-mono" id="fraudTotalParcels">0</p>
              <p class="text-[11px] font-medium text-slate-500 mt-0.5">মোট পার্সেল</p>
            </div>

            <div class="bg-emerald-50/60 rounded-xl p-3.5 border border-emerald-100 text-center">
              <div class="h-8 w-8 rounded-lg bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center mb-1.5">
                <i class="fa-solid fa-circle-check text-sm"></i>
              </div>
              <p class="text-2xl font-bold text-emerald-700 font-mono" id="fraudDeliveredParcels">0</p>
              <p class="text-[11px] font-medium text-emerald-600 mt-0.5">সফল ডেলিভারি</p>
            </div>

            <div class="bg-rose-50/60 rounded-xl p-3.5 border border-rose-100 text-center">
              <div class="h-8 w-8 rounded-lg bg-rose-100 text-rose-600 mx-auto flex items-center justify-center mb-1.5">
                <i class="fa-solid fa-circle-xmark text-sm"></i>
              </div>
              <p class="text-2xl font-bold text-rose-700 font-mono" id="fraudCancelledParcels">0</p>
              <p class="text-[11px] font-medium text-rose-600 mt-0.5">বাতিল / রিটার্ন</p>
            </div>

            <div class="bg-indigo-50/60 rounded-xl p-3.5 border border-indigo-100 text-center">
              <div class="h-8 w-8 rounded-lg bg-indigo-100 text-indigo-600 mx-auto flex items-center justify-center mb-1.5">
                <i class="fa-solid fa-chart-pie text-sm"></i>
              </div>
              <p class="text-2xl font-bold text-indigo-700 font-mono" id="fraudSuccessRatio">0%</p>
              <p class="text-[11px] font-medium text-indigo-600 mt-0.5">সফলতার হার</p>
            </div>
          </div>

          <!-- Success Ratio Progress Bar -->
          <div class="bg-slate-50/70 p-3.5 rounded-xl border border-slate-100">
            <div class="flex items-center justify-between text-xs text-slate-600 mb-1.5 font-medium">
              <span>ডেলিভারি অনুপাত</span>
              <span id="fraudRatioLabel" class="font-bold text-slate-800">০% সফলতা</span>
            </div>
            <div class="h-3 bg-slate-200 rounded-full overflow-hidden flex">
              <div id="fraudProgressBarSuccess" class="bg-emerald-500 h-full transition-all duration-500" style="width: 0%"></div>
              <div id="fraudProgressBarCancel" class="bg-rose-400 h-full transition-all duration-500" style="width: 0%"></div>
            </div>
            <div class="flex justify-between items-center text-[10px] text-slate-400 mt-1">
              <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-emerald-500 inline-block"></span> সফল</span>
              <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-rose-400 inline-block"></span> বাতিল</span>
            </div>
          </div>

          <!-- Merchant Fraud Complaints / Reports Section -->
          <div id="fraudReportsSection" class="hidden">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                <h4 class="font-bold text-sm text-slate-900">মার্চেন্ট ফ্রড ও অভিযোগ রিপোর্ট</h4>
              </div>
              <span id="fraudReportsCountBadge" class="text-xs font-semibold px-2 py-0.5 rounded-full bg-rose-100 text-rose-700">0টি রিপোর্ট</span>
            </div>
            <div id="fraudReportsList" class="space-y-2.5 max-h-56 overflow-y-auto pr-1">
              <!-- Dynamically populated -->
            </div>
          </div>

          <!-- Couriers Breakdown (Pathao, SteadFast, RedX, PaperFly, CarryBee, etc.) -->
          <div>
            <div class="flex items-center justify-between mb-3">
              <h4 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-truck text-slate-500"></i>
                কুরিয়ারভিত্তিক বিস্তারিত হিসাব
              </h4>
              <span class="text-xs text-slate-400">সর্বমোট কুরিয়ার পার্সেল হিস্ট্রি</span>
            </div>

            <div id="fraudCouriersGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
              <!-- Dynamically populated -->
            </div>
          </div>

        </div>
      </div>

      <!-- Modal Footer -->
      <div class="p-4 sm:p-5 border-t border-slate-100 bg-slate-50 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
          <a id="fraudCallAction" href="#" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg transition shadow-xs">
            <i class="fa-solid fa-phone text-slate-500"></i>
            <span>কল করুন</span>
          </a>
          <a id="fraudWaAction" href="#" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition shadow-xs">
            <i class="fa-brands fa-whatsapp text-emerald-600"></i>
            <span>হোয়াটসঅ্যাপ</span>
          </a>
        </div>

        <button type="button" onclick="closeFraudModal()" class="px-5 py-2 text-xs font-semibold text-white bg-slate-800 hover:bg-slate-900 rounded-lg transition">
          বন্ধ করুন
        </button>
      </div>

    </div>
  </div>
</div>

<script>
  let currentFraudPhone = '';
  let currentFraudCustomerName = '';

  function openFraudModal(phone, customerName = '', forceFresh = false) {
    if (!phone) return;

    currentFraudPhone = phone;
    currentFraudCustomerName = customerName;

    const modal = document.getElementById('bdCourierFraudModal');
    const loading = document.getElementById('fraudModalLoading');
    const errorBox = document.getElementById('fraudModalError');
    const content = document.getElementById('fraudModalContent');

    document.getElementById('fraudModalPhone').textContent = phone;
    document.getElementById('fraudModalCustomerName').textContent = customerName ? `(${customerName})` : '';
    document.getElementById('fraudCallAction').href = `tel:${phone}`;

    const cleanDigits = phone.replace(/\D/g, '');
    const waDigits = cleanDigits.startsWith('880') ? cleanDigits : '880' + cleanDigits.replace(/^0+/, '');
    document.getElementById('fraudWaAction').href = `https://wa.me/${waDigits}`;

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');

    loading.classList.remove('hidden');
    errorBox.classList.add('hidden');
    content.classList.add('hidden');

    fetchFraudData(phone, forceFresh);
  }

  function closeFraudModal() {
    const modal = document.getElementById('bdCourierFraudModal');
    if (modal) {
      modal.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }
  }

  function refreshFraudModalData() {
    if (currentFraudPhone) {
      const refreshBtn = document.getElementById('fraudRefreshBtn');
      if (refreshBtn) {
        refreshBtn.classList.add('opacity-60', 'pointer-events-none');
        refreshBtn.querySelector('i').classList.add('animate-spin');
      }
      openFraudModal(currentFraudPhone, currentFraudCustomerName, true);
    }
  }

  function copyFraudPhone() {
    if (!currentFraudPhone) return;
    navigator.clipboard.writeText(currentFraudPhone).then(() => {
      alert('নম্বর কপি করা হয়েছে: ' + currentFraudPhone);
    });
  }

  // Keyboard close
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      closeFraudModal();
    }
  });

  async function fetchFraudData(phone, forceFresh = false) {
    const url = `{{ route('admin.fraud-check.lookup') }}?phone=${encodeURIComponent(phone)}${forceFresh ? '&fresh=1' : ''}`;
    const refreshBtn = document.getElementById('fraudRefreshBtn');

    try {
      const res = await fetch(url, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      const json = await res.json();

      if (refreshBtn) {
        refreshBtn.classList.remove('opacity-60', 'pointer-events-none');
        refreshBtn.querySelector('i').classList.remove('animate-spin');
      }

      if (!res.ok || !json.success) {
        showFraudError(json.message || 'BD Courier থেকে তথ্য পাওয়া যায়নি।');
        return;
      }

      renderFraudModal(json.data);
      updateOrderRowBadges(phone, json.data);
    } catch (err) {
      if (refreshBtn) {
        refreshBtn.classList.remove('opacity-60', 'pointer-events-none');
        refreshBtn.querySelector('i').classList.remove('animate-spin');
      }
      showFraudError('সার্ভারে যোগাযোগ করতে ব্যর্থ হয়েছে। ইন্টারনেট বা সংযোগ চেক করুন।');
    }
  }

  function showFraudError(msg) {
    document.getElementById('fraudModalLoading').classList.add('hidden');
    document.getElementById('fraudModalContent').classList.add('hidden');
    const errBox = document.getElementById('fraudModalError');
    document.getElementById('fraudModalErrorMessage').textContent = msg;
    errBox.classList.remove('hidden');
  }

  function renderFraudModal(data) {
    document.getElementById('fraudModalLoading').classList.add('hidden');
    document.getElementById('fraudModalError').classList.add('hidden');
    const content = document.getElementById('fraudModalContent');
    content.classList.remove('hidden');

    const verdict = data.verdict || {};

    // Header badge & top accent
    const verdictBadge = document.getElementById('fraudModalVerdictBadge');
    verdictBadge.textContent = verdict.badge || 'যাচাইকৃত';
    verdictBadge.className = `text-xs font-semibold px-2.5 py-0.5 rounded-full border ${verdict.badge_color || 'bg-slate-100 text-slate-700'}`;

    const topBar = document.getElementById('fraudModalBar');
    const iconContainer = document.getElementById('fraudModalIconContainer');
    const modalIcon = document.getElementById('fraudModalIcon');

    if (verdict.level === 'safe') {
      topBar.className = 'h-1.5 bg-gradient-to-r from-emerald-400 to-emerald-600';
      iconContainer.className = 'h-12 w-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 shadow-xs';
      modalIcon.className = 'fa-solid fa-shield-check text-xl';
    } else if (verdict.level === 'medium_risk') {
      topBar.className = 'h-1.5 bg-gradient-to-r from-amber-400 to-amber-600';
      iconContainer.className = 'h-12 w-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 shadow-xs';
      modalIcon.className = 'fa-solid fa-triangle-exclamation text-xl';
    } else if (verdict.level === 'high_risk') {
      topBar.className = 'h-1.5 bg-gradient-to-r from-rose-500 to-red-700';
      iconContainer.className = 'h-12 w-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 shadow-xs';
      modalIcon.className = 'fa-solid fa-circle-exclamation text-xl';
    } else {
      topBar.className = 'h-1.5 bg-gradient-to-r from-slate-400 to-slate-600';
      iconContainer.className = 'h-12 w-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0 shadow-xs';
      modalIcon.className = 'fa-solid fa-circle-question text-xl';
    }

    // Verdict action banner
    document.getElementById('fraudVerdictAction').textContent = verdict.action || 'পরামর্শ ও পর্যবেক্ষণ';
    document.getElementById('fraudCheckedAt').textContent = data.checked_at ? `যাচাই: ${data.checked_at}` : '';

    const reasonsList = document.getElementById('fraudVerdictReasons');
    reasonsList.innerHTML = '';
    if (verdict.reasons && verdict.reasons.length > 0) {
      verdict.reasons.forEach(r => {
        const li = document.createElement('li');
        li.textContent = r;
        reasonsList.appendChild(li);
      });
    }

    // Metrics
    document.getElementById('fraudTotalParcels').textContent = data.total_parcel;
    document.getElementById('fraudDeliveredParcels').textContent = data.success_parcel;
    document.getElementById('fraudCancelledParcels').textContent = data.cancelled_parcel;
    document.getElementById('fraudSuccessRatio').textContent = `${data.success_ratio}%`;

    // Progress bar
    const successWidth = data.total_parcel > 0 ? (data.success_parcel / data.total_parcel) * 100 : 0;
    const cancelWidth = data.total_parcel > 0 ? (data.cancelled_parcel / data.total_parcel) * 100 : 0;
    document.getElementById('fraudProgressBarSuccess').style.width = `${successWidth}%`;
    document.getElementById('fraudProgressBarCancel').style.width = `${cancelWidth}%`;
    document.getElementById('fraudRatioLabel').textContent = `${data.success_ratio}% সফলতা (${data.success_parcel} ডেলিভারি, ${data.cancelled_parcel} বাতিল)`;

    // Reports section
    const reportsSection = document.getElementById('fraudReportsSection');
    const reportsList = document.getElementById('fraudReportsList');
    if (data.reports && data.reports.length > 0) {
      reportsSection.classList.remove('hidden');
      document.getElementById('fraudReportsCountBadge').textContent = `${data.reports.length}টি রিপোর্ট`;
      reportsList.innerHTML = '';
      data.reports.forEach(rep => {
        const item = document.createElement('div');
        item.className = 'p-3 rounded-xl bg-rose-50/70 border border-rose-100 text-left';
        item.innerHTML = `
          <div class="flex items-center justify-between text-xs mb-1">
            <div class="flex items-center gap-2">
              <span class="font-bold text-rose-900">${escapeHtml(rep.name || 'মার্চেন্ট')}</span>
              ${rep.courier_name ? `<span class="px-1.5 py-0.5 rounded bg-white text-[10px] font-medium text-slate-600 border border-rose-200">${escapeHtml(rep.courier_name)}</span>` : ''}
            </div>
            <span class="text-[11px] text-slate-400">${escapeHtml(rep.created_at || '')}</span>
          </div>
          <p class="text-xs text-rose-800 leading-relaxed font-sans">${escapeHtml(rep.details || 'কোনো বিবরণ নেই')}</p>
        `;
        reportsList.appendChild(item);
      });
    } else {
      reportsSection.classList.add('hidden');
    }

    // Couriers grid
    const couriersGrid = document.getElementById('fraudCouriersGrid');
    couriersGrid.innerHTML = '';
    if (data.couriers && Object.keys(data.couriers).length > 0) {
      Object.values(data.couriers).forEach(c => {
        const card = document.createElement('div');
        const hasParcels = c.total_parcel > 0;
        card.className = `p-3 rounded-xl border transition-all ${hasParcels ? 'bg-white border-slate-200 shadow-xs' : 'bg-slate-50/60 border-slate-100 opacity-60'}`;

        let ratioBadgeClass = 'bg-slate-100 text-slate-500';
        if (hasParcels) {
          if (c.success_ratio >= 80) ratioBadgeClass = 'bg-emerald-100 text-emerald-700';
          else if (c.success_ratio >= 50) ratioBadgeClass = 'bg-amber-100 text-amber-700';
          else ratioBadgeClass = 'bg-rose-100 text-rose-700';
        }

        card.innerHTML = `
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
              ${c.logo ? `<img src="${escapeHtml(c.logo)}" alt="${escapeHtml(c.name)}" class="h-6 w-6 object-contain rounded-md">` : `<div class="h-6 w-6 bg-slate-200 rounded flex items-center justify-center text-[10px] font-bold text-slate-600">${c.name.substring(0, 2).toUpperCase()}</div>`}
              <span class="text-xs font-bold text-slate-800">${escapeHtml(c.name)}</span>
            </div>
            <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full ${ratioBadgeClass}">
              ${hasParcels ? `${c.success_ratio}%` : '০'}
            </span>
          </div>
          <div class="grid grid-cols-3 text-center text-[11px] gap-1 pt-1.5 border-t border-slate-100">
            <div>
              <p class="text-[10px] text-slate-400">মোট</p>
              <p class="font-bold text-slate-700">${c.total_parcel}</p>
            </div>
            <div>
              <p class="text-[10px] text-emerald-500">সফল</p>
              <p class="font-bold text-emerald-700">${c.success_parcel}</p>
            </div>
            <div>
              <p class="text-[10px] text-rose-400">বাতিল</p>
              <p class="font-bold text-rose-700">${c.cancelled_parcel}</p>
            </div>
          </div>
        `;
        couriersGrid.appendChild(card);
      });
    }
  }

  function updateOrderRowBadges(phone, data) {
    const selector = `.js-fraud-check-btn[data-phone="${phone}"]`;
    const buttons = document.querySelectorAll(selector);

    buttons.forEach(btn => {
      const verdict = data.verdict || {};
      let colorClass = 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200';

      if (verdict.level === 'safe') {
        colorClass = 'bg-emerald-50 text-emerald-700 border-emerald-300 hover:bg-emerald-100';
      } else if (verdict.level === 'medium_risk') {
        colorClass = 'bg-amber-50 text-amber-800 border-amber-300 hover:bg-amber-100';
      } else if (verdict.level === 'high_risk') {
        colorClass = 'bg-rose-50 text-rose-700 border-rose-300 hover:bg-rose-100';
      }

      btn.className = `js-fraud-check-btn inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-medium transition-all border shadow-xs ${colorClass}`;

      let labelText = '';
      if (data.total_parcel === 0) {
        labelText = 'নতুন কাস্টমার (০)';
      } else {
        labelText = `মোট ${data.total_parcel} | বাতিল ${data.cancelled_parcel} (${data.success_ratio}%)`;
      }

      if (data.reports_count > 0) {
        labelText += ` ⚠️ ${data.reports_count} ফ্রড`;
      }

      btn.innerHTML = `
        <i class="fa-solid ${verdict.icon || 'fa-shield-halved'} text-[10px]"></i>
        <span>${labelText}</span>
      `;
    });
  }

  function escapeHtml(text) {
    if (!text) return '';
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.toString().replace(/[&<>"']/g, m => map[m]);
  }
</script>
