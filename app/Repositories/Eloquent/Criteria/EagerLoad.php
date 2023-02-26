<?php

  use App\Repositories\Criteria\ICriterion;

  class EagerLoad implements ICriterion {

    protected string $relationships;

    public function __construct($relationships)
    {
      $this->relationships = $relationships;
    }

    public function apply($model)
    {
      return $model->with($this->relationships);
    }
  }
