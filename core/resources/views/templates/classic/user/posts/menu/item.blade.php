@if(count($menus))
    <div class="cat-menu-items sortable-container" data-reorder-route="{{route('restaurants.reorderMenuItem', $post->id)}}">
        @foreach($menus as $menu)
            <div class="dashboard-box margin-top-0 margin-bottom-15" data-id="{{$menu->id}}">
                <div class="headline small">
                    <h3><i class="icon-feather-menu quickad-js-handle"></i>
                        <img class="menu-avatar margin-right-5" src="{{asset('storage/menu/'.$menu->image)}}"
                             alt="{{$menu->name}}"> {{$menu->name}}
                    </h3>
                    <div class="margin-left-auto line-height-1">
                        <a href="#" data-id="{{$menu->id}}" data-catid="{{$menu->category_id}}" data-data="{{str($menu)->jsonSerialize()}}"
                           class="button ripple-effect btn-sm edit-menu-item"
                           title="{{___('Edit Menu')}}" data-tippy-placement="top"><i class="icon-feather-edit margin-left-0"></i></a>
                        <a href="{{route('restaurants.menuItemExtras', ['restaurant' => $post->id, 'menu' => $menu->id])}}" class="button ripple-effect btn-sm"
                           title="{{___('Extra & Variants')}}" data-tippy-placement="top"><i
                                class="icon-feather-layers margin-left-0"></i></a>
                        <a href="{{ route('restaurants.deleteMenuItem', $post->id) }}" data-id="{{$menu->id}}"
                           class="button red ripple-effect btn-sm delete-item"
                           title="{{___('Delete Menu')}}"
                           data-tippy-placement="top"><i class="icon-feather-trash-2 margin-left-0"></i></a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
