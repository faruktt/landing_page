@php $districts = ['ঢাকা', 'ঢাকার বাইরে']; @endphp
<option value="" disabled {{ $selected ? '' : 'selected' }}>ডেলিভারি এলাকা বাছাই করুন</option>
@foreach ($districts as $district)
  <option value="{{ $district }}" {{ $selected === $district ? 'selected' : '' }}>{{ $district }}</option>
@endforeach
