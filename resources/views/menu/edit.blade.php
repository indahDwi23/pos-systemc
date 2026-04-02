@extends('layouts.main')

@section('container')
<h1 class="app-page-title">Edit Menu</h1>
<form class="settings-form" action="/menu/{{ $menu->id }}" method="post" enctype="multipart/form-data">
    @csrf
    @method('put')
    <div class="row g-3 settings-section">
        @error('picture')
            <p class="small text-danger">{{ $message }}</p>
        @enderror
        <div class="col-12 col-md-4 picture-container" style="display: flex; flex-direction: column; justify-content: center;">
            <img src="{{ asset('storage/'.$menu->picture) }}" alt="" class="picture-preview" style="width: 100%; height: 250px !important; object-fit: cover !important; border-radius: 10px; max-width: 100%;">
            <input type="file" id="select-picture" name="picture">
            <div class="black-screen">{{ $menu->picture }} <p> klik untuk mengubah </p></div>
        </div>
        <div class="col-12 col-md-8">
            <div class="app-card app-card-settings p-4">
                <div class="app-card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Menu</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', $menu->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="modal" class="form-label">Modal</label>
                        <input type="text" class="form-control @error('modal') is-invalid @enderror" id="modal" name="modal" value="{{ old('modal', number_format($menu->modal,0,',','.')) }}" required>
                        @error('modal')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Harga</label>
                        <input type="text" name="price" class="form-control" id="price" value="{{ old('price', number_format($menu->price,0,',','.') )}}" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Kategori</label>
                        <select class="form-select" name="category" id="category">
                            @if ( old('category', $menu->category) == 'food' )
                                <option value="food" selected >Makanan</option>
                                <option value="drink">Minuman</option>
                            @else
                                <option value="food">Makanan</option>
                                <option value="drink" selected>Minuman</option>
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        @error('description')
                            <p class="small text-danger">{{ $message }}</p>
                        @enderror
                        <input id="description" type="hidden" name="description" value="{{ old('description', $menu->description) }}">
                        <trix-editor input="description"></trix-editor>
                    </div>
                    <button type="submit" class="btn app-btn-info" >Simpan Perubahan</button>
                    <a href="/menu/" class="btn btn-danger text-white" role="button">Batal</a>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    const select_picture = document.getElementById('select-picture');
    const input_picture = document.getElementById('input-picture');
    const picture_preview = document.querySelector('.picture-preview');
    const black_screen = document.querySelector('.black-screen');

    select_picture.addEventListener('change', function () {
        const files = select_picture.files[0];
        const fileReader = new FileReader();
        fileReader.readAsDataURL(files);
        fileReader.addEventListener("load", function () {
            picture_preview.src = this.result;
            black_screen.innerHTML = `${files.name} <p> klik untuk mengubah </p>`
        });
    })
    
</script>
<script src="/js/formatmoney.js"></script>
@endsection