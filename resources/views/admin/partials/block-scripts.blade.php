<script>
  document.querySelectorAll('.js-toggle-block').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var isBlocked = form.dataset.blocked === '1';

      if (isBlocked) {
        Swal.fire({
          title: form.dataset.name + ' কে আনব্লক করবেন?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'হ্যাঁ, আনব্লক করুন',
          cancelButtonText: 'বাতিল',
          confirmButtonColor: '#4f46e5',
          reverseButtons: true,
        }).then(function (result) {
          if (result.isConfirmed) form.submit();
        });
      } else {
        Swal.fire({
          title: form.dataset.name + ' কে ব্লক করবেন?',
          text: 'ব্লক করা হলে এই কাস্টমার আর অনলাইনে অর্ডার করতে পারবেন না, হোয়াটসঅ্যাপ বাটন দেখানো হবে।',
          input: 'text',
          inputLabel: 'কারণ (ঐচ্ছিক)',
          inputPlaceholder: 'যেমন: ফেক অর্ডার / প্রতারণা',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'ব্লক করুন',
          cancelButtonText: 'বাতিল',
          confirmButtonColor: '#dc2626',
          reverseButtons: true,
        }).then(function (result) {
          if (result.isConfirmed) {
            form.querySelector('.reason-input').value = result.value || '';
            form.submit();
          }
        });
      }
    });
  });

  document.querySelectorAll('.js-block-ip').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      Swal.fire({
        title: form.dataset.ip + ' আইপি ব্লক করবেন?',
        text: 'এই আইপি থেকে ৩০ মিনিটের জন্য কেউ অর্ডার করতে পারবে না।',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ব্লক করুন',
        cancelButtonText: 'বাতিল',
        confirmButtonColor: '#dc2626',
        reverseButtons: true,
      }).then(function (result) {
        if (result.isConfirmed) form.submit();
      });
    });
  });
</script>
