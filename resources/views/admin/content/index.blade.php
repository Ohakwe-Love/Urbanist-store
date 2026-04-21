@extends('admin.layout')

@section('title', 'Content | Admin')
@section('heading', 'Content Management')
@section('subheading', 'Keep banners, promos, about copy, and contact content current.')

@section('content')
    <div class="admin-card">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Block</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($blocks as $block)
                        <tr>
                            <td>
                                <strong>{{ $block->title }}</strong>
                                <div class="helper-text">{{ $block->key }}</div>
                            </td>
                            <td><span class="badge {{ $block->is_active ? 'badge-success' : 'badge-danger' }}">{{ $block->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td><a href="{{ route('admin.content.edit', $block) }}" class="btn-link">Edit block</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
