<?php

namespace AutoDTO\Helper\Http\Controller;

abstract class BaseController extends \Illuminate\Routing\Controller
{
    public function __construct()
    {
        $this->middleware("autoDTO.hook");
    }
}
