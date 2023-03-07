<?php

	namespace App\Repositories\Contracts;

	interface IDesign
	{
    public function addComment(int $designId, array $data);
    public function like(int $id);
    public function isLikedByUser(int $id);
	}
