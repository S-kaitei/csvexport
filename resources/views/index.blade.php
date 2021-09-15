@extends('layouts.app')

@section('css')
{{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.1.1/remodal-default-theme.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.1.1/remodal.css" rel="stylesheet" /> --}}
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.1.1/remodal.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr" defer></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/l10n/ja.js" defer></script>
<script src="{{ asset('js/index.js') }}" defer></script>
@endsection

@section('content')
<table class="table">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">建築物条件</th>
      <th scope="col">建築物デザイン</th>
      <th scope="col">名前</th>
      <th scope="col">住所</th>
      <th scope="col">電話番号</th>
      <th scope="col">Fax</th>
    </tr>
  </thead>
  <tbody class="table-stripes-row-tbody">
  @foreach($contacts as $c)
    <tr>
      <td>{{ $c->id }}</td>
      <td>{{ $c->condition_name }}</td>
      <td>{{ $c->design_name }}</td>
      <td>{{ $c->surname }}{{ $c->name }}</td>
      <td>{{ $c->zipcode }}<br>{{ $c->pref }}{{ $c->city }}{{ $c->street }}</td>
      <td>{{ $c->tel_number }}</td>
      <td>{{ $c->fax_number }}</td>
    </tr>
  @endforeach
  </tbody>
</table>

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
  CSVエクスポート
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">エクスポートする期間を決めてください</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id='csvform' action="{{ route('contact.csv.export') }}" method="POST">
            @csrf
            <div class='row mb-3'>
                <div class="col-md-5">
                    <label class='h4'>エクスポートの開始日</label>
                </div>
                <div class="col-md-2 h2 d-flex justify-content-center align-items-center"></div>
                <div class="col-md-5">
                    <label class='h4'>エクスポートの終端日</label>
                </div>
            </div>
            <div class='row mb-3'>
                <div class="col-md-5">
                    <input data-provide="datepicker" class="form-control datepicker js-start-date" type="datetime"
                    placeholder="出力開始日" name="start_date" value="" dusk='datepicker_first'>
                </div>
                <div class="col-md-2 h2 d-flex justify-content-center align-items-center">
                    〜
                </div>
                <div class="col-md-5">
                    <input data-provide="datepicker" class="form-control datepicker js-end-date" type="datetime" placeholder="出力終了日"
                    name="end_date" value="" dusk='datepicker_last'>
                </div>
            </div>
        <div class="modal-footer">
            <button data-remodal-action="cancel" class="btn btn-secondary">キャンセル</button>
            <button type='submit' class="btn btn-primary">ダウンロード</button>
        </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="remodal w80" data-remodal-id="modal">

</div>

@endsection
