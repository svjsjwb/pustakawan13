@extends('layouts.app')

@section('title', 'Kategori')

@section('content')

<div class="page-header">
    <h2>Manajemen Kategori</h2>

    <a href="{{ route('categories.create') }}" class="btn-add-top">
        + Tambah Kategori
    </a>
</div>

@if(session('success'))
    <div class="alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th width="180">Aksi</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($categories as $category)

                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>{{ $category->name }}</td>

                        <td>{{ $category->description }}</td>

                        <td>

                            <a href="{{ route('categories.edit', $category) }}">
                                Edit
                            </a>

                            |

                            <form
                                action="{{ route('categories.destroy', $category) }}"
                                method="POST"
                                style="display:inline;">

                                @csrf
                                @method('DELETE')

                                <button type="submit">
                                    Hapus
                                </button>

                            </form>

                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="4">
                            Belum ada kategori.
                        </td>
                    </tr>

                @endforelse

            </tbody>
        </table>
    </div>
</div>

@endsection