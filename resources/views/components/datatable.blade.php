@props([
    'id',
    'columns' => [],
])

<div class="table-responsive">
    <table id="{{ $id }}" class="table table-striped table-hover w-100">
        <thead>
            <tr>
                @foreach($columns as $column)
                    <th>{{ __($column) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
