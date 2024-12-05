<div class="slidePanel-content">
    <header class="slidePanel-header">
        <div class="slidePanel-overlay-panel">
            <div class="slidePanel-heading">
                <h2>{{ $advertisement->provider_name }}</h2>
            </div>
            <div class="slidePanel-actions">
                <button id="post_sidePanel_data" class="btn btn-icon btn-primary" title="{{___('Save')}}">
                    <i class="icon-feather-check"></i>
                </button>
                <button class="btn btn-icon btn-default slidePanel-close" title="{{___('Close')}}">
                    <i class="icon-feather-x"></i>
                </button>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <form action="{{ route('admin.advertisements.update', $advertisement->id) }}" method="post" enctype="multipart/form-data" id="sidePanel_form">
            @csrf
            @method('PUT')
            <div class="mb-3">
                {{quick_switch(___('Status'), 'status', $advertisement->status == '1')}}
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Provider Name') }} *</label>
                <input type="text" name="provider_name" class="form-control" value="{{ $advertisement->provider_name }}" required />
            </div>
            <div class="mb-3">
                <label class="form-label">{{ ___('Code') }} *</label>
                <textarea name="code" class="form-control jsContent" rows="10">{{ $advertisement->code }}</textarea>
            </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    "use strict";
    var element = document.querySelectorAll(".jsContent");

    element.forEach(function (editorEl) {

        var editor = CodeMirror.fromTextArea(editorEl, {
            lineNumbers: true,
            mode: "htmlmixed",
            theme: "monokai",
            keyMap: "sublime",
            autoCloseBrackets: true,
            matchBrackets: true,
            showCursorWhenSelecting: true,
        });

        editor.setSize(null, 200);
        editor.on('change', (editor) => {
            const text = editor.doc.getValue()
            editorEl.value = text;
        });
    });

</script>
