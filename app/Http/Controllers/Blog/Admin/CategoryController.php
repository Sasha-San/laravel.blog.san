<?php

namespace App\Http\Controllers\Blog\Admin;


use App\Http\Requests\BlogCategoryUpdateRequest;
use App\Models\Admin\Category;
use App\Repositories\Admin\CategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use MetaTag;
use Menu as LavMenu;
use SebastianBergmann\CodeCoverage\Driver\Xdebug;

/**
 *  Управление категориями блога
 * Class CategoryController
 * @package App\Http\Controllers\Blog\Admin
 */
class CategoryController extends AdminBaseController
{


    private $categoryRepository;

    /**
     * CategoryController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->categoryRepository = app(CategoryRepository::class);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $arrMenu = Category::all();
        $menu = $this->categoryRepository->buildMenu($arrMenu);

        MetaTag::setTags(['title' => 'Список категорий']);
        return view('blog.admin.category.index',['menu' => $menu]);



    }


    /**
     * Show the form for creating a new resource.
     *
     * @param BlogCategoryUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $item = new Category();
        $categoryList = $this->categoryRepository->getComboBoxCategories();

        MetaTag::setTags(['title' => 'Добавление категории']);
        return view('blog.admin.category.create',compact('item','categoryList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BlogCategoryUpdateRequest $request)
    {
        $data = $request->input();

        $item = (new Category())->create($data);

        if ($item) {
            return redirect()
                ->route('blog.admin.categories.create', [$item->id])
                ->with(['success' => 'Успешно сохранено']);
        } else {
            return back()
                ->withErrors(['msg'=>'Ошибка сохранения'])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @param BlogCategoryRepository $categoryRepository
     * @return \Illuminate\Http\Response
     */
    public function edit($id,CategoryRepository $categoryRepository)
    {
        $item = $this->categoryRepository->getEditId($id);

        if (empty($item)){
            abort(404);
        }

        $categoryList = $this->categoryRepository->getComboBoxCategories();

        MetaTag::setTags(['title' => 'Редактирование категории']);
        return view('blog.admin.category.edit',compact('item'),compact('categoryList'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function update(BlogCategoryUpdateRequest $request, $id)
    {
        $item = $this->categoryRepository->getEditId($id);

        if (empty($item)){
            return back()
                ->withErrors(['msg' => "Запись = [{$id}] не найдена"])
                ->withInput();
        }

        $data = $request->all();


        $result = $item->update($data);

        if ($result){
            return redirect()
                ->route('blog.admin.categories.edit', $item->id)
                ->with(['success' => "Успешно сохранено"]);
        } else {
            return back()
                ->withErrors(['msg' => 'Ошибка сохранения!'])
                ->withInput();
        }

    }

    /** @throws \Exception */
    public function mydel()
    {
        $id = $this->categoryRepository->getRequestID();
        if (!$id){
            return back()
                ->withErrors(['msg'=>'Ошибка с ID']);
        }

        $children = $this->categoryRepository->checkChildren($id);
        if ($children){
                return back()
                    ->withErrors(['msg'=>'Удаление невозможно, в категории есть вложенные категории']);
        }

        $parents = $this->categoryRepository->checkParentsInProducts($id);
        if ($parents){
            return back()
                ->withErrors(['msg'=>'Удаление невозможно, в категории есть товары']);
        }

        $delete = $this->categoryRepository->deleteCategory($id);

        if ($delete){
            return redirect()
                ->route('blog.admin.categories.index')
                ->with(['success' => "Запись id [$id] удалена"]);
        }   else {
            return back()->withErrors(['msg' => 'Ошибка удаления']);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return view('blog.admin.category.index');
    }
}
