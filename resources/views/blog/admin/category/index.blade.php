@extends('layouts.app_admin')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        @component('blog.admin.components.breadcrumb')
            @slot('title') Список категорий @endslot
            @slot('parent') Главная @endslot
            @slot('active') Список категорий @endslot
        @endcomponent
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-body">
                        <div>
                            <i style="font-weight: bold; margin-left: 170px; font-size: small;"><small>Для редактирования - нажмите на категорию.</small></i>
                            <i style="font-weight: bold; text-align: right; margin-left: 170px; font-size: small;"><small> Невозможно удалять категории имеющие наследника.</small></i>
                        </div>
                       <br>
                        @if($menu)
                            <div class="list-group list-group-root well">

                @include('blog.admin.category.menu.customMenuItems', ['items'=>$menu->roots()])

                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->


@endsection

