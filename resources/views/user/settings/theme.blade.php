<div class="container-fluid pushFromTop">
    <div class="row">
            @foreach($data['themes'] as $theme)
            <div class="col-lg-6">
                <div class="card mt-4">
                    <div  class="card-header text-center text-nowrap">
                        <h5>{{ $theme['name']}}</h5>
                    </div>
                    <div class="card-body text-center">
                        <form id="settingsForm" method="POST">

                            @csrf
                        <input type="hidden" name="theme" value="{{ $theme['id'] }}" />
                        <h5 class="card-title"> </h5>

                        <p class="card-text text-center">Primary Color <div style="background-color: {{ $theme['primary'] }}" class="color-preview mx-auto"></div></p>
                        <p class="card-text text-center">Secondary Color <div style="background-color:  {{ $theme['secondary'] }}" class="color-preview mx-auto"></div></p>

                        <button class="btn btn-outline-primary mt-4" theme="{{ $theme['id'] }}">Apply</button>
                        </form>
                    </div>
                    <div class="card-footer text-muted">
                       {!! $theme['comment'] !!}
                    </div>
                </div>
            </div>
            @endforeach
    </div>
</div>

