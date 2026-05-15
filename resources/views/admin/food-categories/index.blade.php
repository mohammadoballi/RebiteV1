@extends('layouts.admin')

@section('title', __('food_categories.title'))

@section('content')
<div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2">
    <h1><i class="fas fa-sitemap me-2"></i>{{ __('food_categories.title') }}</h1>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">{{ __('food_categories.add_category') }}</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.food-categories.store') }}" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-4">
                <label class="form-label">{{ __('food_categories.parent_optional') }}</label>
                <select name="parent_id" class="form-select">
                    <option value="">{{ __('food_categories.top_level') }}</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('food_categories.name') }}</label>
                <input type="text" name="name" class="form-control" required maxlength="255">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('food_categories.sort') }}</label>
                <input type="number" name="sort_order" class="form-control" value="0" min="0">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success w-100"><i class="fas fa-plus me-1"></i>{{ __('food_categories.add') }}</button>
            </div>
        </form>
    </div>
</div>

@foreach($parents as $parent)
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <strong><i class="fas fa-folder text-success me-1"></i>{{ $parent->name }}</strong>
        <form method="POST" action="{{ route('admin.food-categories.update', $parent) }}" class="d-flex flex-wrap gap-2 align-items-center">
            @csrf
            @method('PUT')
            <input type="hidden" name="parent_id" value="">
            <input type="text" name="name" value="{{ $parent->name }}" class="form-control form-control-sm" style="max-width:220px" required>
            <input type="number" name="sort_order" value="{{ $parent->sort_order }}" class="form-control form-control-sm" style="width:90px">
            <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('general.save') }}</button>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __('food_categories.name') }}</th>
                    <th style="width:120px">{{ __('food_categories.sort') }}</th>
                    <th style="width:200px">{{ __('general.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($parent->children as $child)
                <tr>
                    <td>
                        <form method="POST" action="{{ route('admin.food-categories.update', $child) }}" class="d-flex flex-wrap gap-2 align-items-center">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="parent_id" value="{{ $parent->id }}">
                            <input type="text" name="name" value="{{ $child->name }}" class="form-control form-control-sm" style="max-width:280px" required>
                            <input type="number" name="sort_order" value="{{ $child->sort_order }}" class="form-control form-control-sm" style="width:90px">
                            <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('general.save') }}</button>
                        </form>
                    </td>
                    <td>{{ $child->sort_order }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.food-categories.destroy', $child) }}" class="d-inline" onsubmit="return confirm('{{ __('food_categories.delete_confirm') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-muted px-3 py-2">{{ __('food_categories.no_subcategories') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        <form method="POST" action="{{ route('admin.food-categories.destroy', $parent) }}" class="d-inline" onsubmit="return confirm('{{ __('food_categories.delete_parent_confirm') }}');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger" @if($parent->children->isNotEmpty()) disabled title="{{ __('food_categories.remove_children_first') }}" @endif>
                <i class="fas fa-trash me-1"></i>{{ __('food_categories.delete_parent') }}
            </button>
        </form>
    </div>
</div>
@endforeach

@if($parents->isEmpty())
<div class="alert alert-info">{{ __('food_categories.empty') }}</div>
@endif
@endsection
