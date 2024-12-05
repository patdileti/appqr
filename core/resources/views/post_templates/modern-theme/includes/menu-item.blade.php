@if(count($menus))
        @foreach($menus as $key => $menu)
            {{-- Break loop if menu limit exceeded --}}
            @if($plan->settings->menu_limit != "999"
                    && $key >= $plan->settings->menu_limit)
                @break
            @endif
            <div class="section-menu" data-id="{{$menu->id}}"
                 data-name="{{$menu->name}}" data-price="{{ price_symbol_format($menu->price)}}"
                 data-amount="{{$menu->price}}"
                 data-description="{{$menu->description}}" data-image-url="{{asset('storage/menu/'.$menu->image)}}">
                <div class="menu-item list">

                    @if($menu->image != 'default.png')
                        <div class="menu-image menu-lightbox">
                            <img class="lazy-load menu-lightbox-image"
                                 src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
                                 data-original="{{asset('storage/menu/'.$menu->image)}}" alt="{{$menu->name}}"
                                 data-src="{{asset('storage/menu/'.$menu->image)}}"
                                 data-sub-html="{{$menu->description}}">
                            <div class="badge abs {{$menu->type}}"><i class="fa fa-circle"></i></div>
                        </div>
                    @else
                        <div class="badge only {{$menu->type}}"><i class="fa fa-circle"></i></div>
                    @endif

                    <div class="menu-content">
                        <div class="menu-detail">
                            <div class="menu-title">
                                <h4>{{$menu->name}}</h4>
                                <div class="menu-price">{{ price_symbol_format($menu->price)}}</div>
                            </div>
                            @if($allow_order)
                            <div class="add-menu">
                                <div class="add-btn add-item-to-order">
                                    <span>{{ ___('Add') }}</span>
                                    <i class="icon-feather-plus"></i>
                                </div>
                                @if($menu->variants->isNotEmpty() || $menu->extras->isNotEmpty())
                                    <span class="customize">{{ ___('customizable') }}</span>
                                @endif
                            </div>
                            @endif
                        </div>
                        <div class="menu-recipe">{{$menu->description}}</div>
                        @include('post_templates.allergies')
                    </div>
                </div>
            </div>
        @endforeach
@endif
