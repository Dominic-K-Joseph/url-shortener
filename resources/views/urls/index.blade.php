@extends('layouts.app')

@section('title', 'My URLs')
@section('page-title', 'My URLs')

@section('content')

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Shorten a New URL</h3>
        </div>
        <div class="card-body">
            <div id="form-alert" class="alert d-none"></div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" id="url-title" class="form-control" placeholder="e.g. My Blog">
                        <small class="text-danger d-none" id="err-title"></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Long URL</label>
                        <input type="text" id="url-original" class="form-control"
                            placeholder="https://example.com/very/long/path">
                        <small class="text-danger d-none" id="err-url"></small>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-group w-100">
                        <button id="btn-shorten" class="btn btn-primary btn-block">
                            <i class="fas fa-compress-alt"></i> Shorten
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Shortened URLs</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Original URL</th>
                        <th>Short URL</th>
                        <th>Created</th>
                        <th>Copy</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody id="url-table-body">
                    @forelse($urls as $index => $url)
                        <tr id="row-{{ $url->id }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $url->title }}</td>
                            <td>
                                <a href="{{ $url->original_url }}" target="_blank" class="text-truncate d-inline-block"
                                    style="max-width:220px;">
                                    {{ $url->original_url }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ $url->short_url }}" target="_blank">{{ $url->short_url }}</a>
                            </td>
                            <td>{{ $url->created_at->format('d M Y') }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary btn-copy" data-url="{{ $url->short_url }}">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $url->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-row">
                            <td colspan="7" class="text-center text-muted py-4">No URLs yet. Shorten one above!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')

    <script>
        $(function() {

            $(document).on('click', '.btn-copy', function() {
                var url = $(this).data('url');
                navigator.clipboard.writeText(url).then(function() {
                    showAlert('Copied to clipboard!', 'success');
                });
            });

            $('#btn-shorten').on('click', function() {
                clearErrors();

                var title = $('#url-title').val().trim();
                var originalUrl = $('#url-original').val().trim();
                var btn = $(this);

                if (!title) {
                    showFieldError('err-title', 'Title is required.');
                    return;
                }
                if (!originalUrl) {
                    showFieldError('err-url', 'URL is required.');
                    return;
                }
                if (!isValidUrl(originalUrl)) {
                    showFieldError('err-url',
                        'Please enter a valid URL (must start with http:// or https://).');
                    return;
                }

                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Shortening...');

                $.ajax({
                    url: '{{ route('urls.store') }}',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        title: title,
                        original_url: originalUrl,
                    },
                    success: function(res) {
                        if (res.success) {
                            prependRow(res.url);
                            $('#url-title').val('');
                            $('#url-original').val('');
                            showAlert('URL shortened successfully!', 'success');
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON && xhr.responseJSON.errors;
                        if (errors) {
                            if (errors.title) showFieldError('err-title', errors.title[0]);
                            if (errors.original_url) showFieldError('err-url', errors
                                .original_url[0]);
                        } else {
                            showAlert('Something went wrong. Please try again.', 'danger');
                        }
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(
                            '<i class="fas fa-compress-alt"></i> Shorten');
                    }
                });
            });

            function prependRow(url) {
                $('#empty-row').remove();
                var rowCount = $('#url-table-body tr').length + 1;
                var row = `
                    <tr id="row-${url.id}">
                        <td>${rowCount}</td>
                        <td>${url.title}</td>
                        <td><a href="${url.original_url}" target="_blank">${url.original_url}</a></td>
                        <td><a href="${url.short_url}" target="_blank">${url.short_url}</a></td>
                        <td>${url.created_at}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary btn-copy" data-url="${url.short_url}">
                                <i class="fas fa-copy"></i>
                            </button>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${url.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                $('#url-table-body').prepend(row);
            }

            function isValidUrl(str) {
                try {
                    var u = new URL(str);
                    return u.protocol === 'http:' || u.protocol === 'https:';
                } catch (e) {
                    return false;
                }
            }

            function showAlert(msg, type) {
                var $alert = $('#form-alert');
                $alert.attr('class', 'alert alert-' + type).text(msg);
                setTimeout(function() {
                    $alert.addClass('d-none');
                }, 3500);
            }

            function showFieldError(id, msg) {
                $('#' + id).text(msg).removeClass('d-none');
            }

            function clearErrors() {
                $('.text-danger').addClass('d-none').text('');
            }


            $(document).on('click', '.btn-delete', function() {
                if (!confirm('Are you sure you want to delete this URL?')) return;

                var btn = $(this);
                var id = btn.data('id');

                $.ajax({
                    url: '/urls/' + id,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'DELETE',
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#row-' + id).fadeOut(300, function() {
                                $(this).remove();
                            });
                            showAlert('URL deleted successfully!', 'success');
                        }
                    },
                    error: function() {
                        showAlert('Something went wrong.', 'danger');
                    }
                });
            });

        });
    </script>

@endpush
