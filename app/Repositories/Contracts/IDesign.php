<?php

	namespace App\Repositories\Contracts;

	interface IDesign
	{
    public function addComment($designId, array $data);
	}
