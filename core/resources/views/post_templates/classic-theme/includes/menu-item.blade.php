@if(count($menus))
    <div class="row">
        @foreach($menus as $key => $menu)
            {{-- Break loop if menu limit exceeded --}}
            @if($plan->settings->menu_limit != "999"
                    && $key >= $plan->settings->menu_limit)
                @break
            @endif
            <div
                class="col-xl-3 col-lg-6 col-md-6 col-sm-6 ajax-item-listing menu-grid-view" data-id="{{$menu->id}}"
                data-name="{{$menu->name}}" data-price="{{ price_symbol_format($menu->price)}}"
                data-amount="{{$menu->price}}"
                data-description="{{$menu->description}}" data-image-url="{{asset('storage/menu/'.$menu->image)}}"
                @if(@$postOptions->menu_layout == 'grid') style="display:block" @else style="display:none" @endif>
                <div class="menu_item">
                    <figure>
                        <img class="lazy-load"
                             src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
                             data-original="{{asset('storage/menu/'.$menu->image)}}" alt="{{$menu->name}}">
                    </figure>
                    <div class="menu_detail">
                        <h4 class="menu_post">
                            <span class="menu_title"><span class="badge only {{$menu->type}}"><i
                                        class="fa fa-circle"></i></span>{{$menu->name}}</span>
                            <span class="menu_price">{{ price_symbol_format($menu->price)}}</span>
                        </h4>
                        <div class="menu_excerpt">
                            <div>{{$menu->description}}</div>
                            @if($allow_order)
                                <div class="margin-left-auto padding-left-10">
                                    <button type="button"
                                            class="button add-item-button add-extras">{{ ___('Add') }}</button>
                                </div>
                            @endif
                        </div>

                        @include('post_templates.allergies')
                    </div>
                </div>
            </div>
            <div
                class="col-lg-6 col-md-6 col-sm-6 ajax-item-listing menu-list-view" data-id="{{$menu->id}}"
                data-name="{{$menu->name}}" data-price="{{ price_symbol_format($menu->price)}}"
                data-amount="{{$menu->price}}"
                data-description="{{$menu->description}}" data-image-url="{{asset('storage/menu/'.$menu->image)}}"
                @if(is_null(@$postOptions->menu_layout) || @$postOptions->menu_layout == 'list' || @$postOptions->menu_layout == 'both') style="display:block" @else style="display:none" @endif>
                <div class="menu_detail">
                    <h4 class="menu_post">
                        <span class="menu_title"><span class="badge only {{$menu->type}}"><i
                                    class="fa fa-circle"></i></span>{{$menu->name}}</span>
                        <span class="menu_dots"></span>
                        <span class="menu_price">{{ price_symbol_format($menu->price)}}</span>
                    </h4>
                    <div class="menu_excerpt">
                        <div>{{$menu->description}}</div>
                        @if($allow_order)
                            <div class="margin-left-auto padding-left-10">
                                <button type="button"
                                        class="button add-item-button add-extras">{{ ___('Add') }}</button>
                            </div>
                        @endif
                    </div>
                    @include('post_templates.allergies')
                </div>
            </div>
        @endforeach
    </div>
@endif
