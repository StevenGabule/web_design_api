<?php


	namespace App\Repositories\Eloquent;


  use App\Exceptions\ModelNotDefined;
  use App\Models\Design;
  use App\Repositories\Contracts\IBase;
  use App\Repositories\Criteria\ICriteria;
  use Error;
  use Illuminate\Contracts\Container\BindingResolutionException;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Support\Arr;

  abstract class BaseRepository implements IBase, ICriteria
	{
    protected Model $model;

    /**
     * @throws BindingResolutionException
     * @throws ModelNotDefined
     */
    public function __construct()
    {
      $this->model = $this->getModelClass();
    }

    /**
     * @throws BindingResolutionException
     * @throws ModelNotDefined
     */
    protected function getModelClass() {
      if (!method_exists($this, 'model')) {
        throw new ModelNotDefined();
      }
      return app()->make($this->model());
    }

    public function all()
    {
      return $this->model->get();
    }

    public function find($id)
    {
      return $this->model->findOrFail($id);
    }

    public function findWhere($column, $value)
    {
      return $this->model->where($column, $value)->get();
    }

    public function findWhereFirst($column, $value)
    {
      return $this->model->where($column, $value)->firstOrFail();
    }

    public function paginate($perPage = 15)
    {
      return $this->model->paginate($perPage);
    }

    public function create(array $data)
    {
      return $this->model->create($data);
    }

    public function update($id, array $data)
    {
      $record = $this->find($id);
      $record->update($data);
      return $record;
    }

    public function delete($id)
    {
      return $this->find($id)->delete();
    }

    public function restore($id)
    {
      return $this->model::withTrashed()->find($id)->restore();
    }

    public function forceDelete($id)
    {
      return $this->model::withTrashed()->find($id)->forceDelete();
    }

    public function withCriteria(...$criteria): static
    {
      $criteria = Arr::flatten($criteria);
      foreach ($criteria as $criterion) {
        $this->model = $criterion->apply($this->model);
      }
      return $this;
    }
  }
