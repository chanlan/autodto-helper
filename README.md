# autodto-helper

> autodto-helper是一个Laravel扩展，支持在Controller中使用自定义的DTO来接收客户端提交的参数信息，无须使用复杂的Request对象，根据业务需求，可以自定义自己的DTO。

#### 1. 安装
```
composer require autodto/helper:1.0.0 --prefer-dist 
```
#### 2. 示例代码
 * 自定义DTO：需要实现扩展包中的DTO接口，必须定义带参的构造器
```php  
<?php

namespace App\Models\DTO;

use AutoDTO\Helper\Models\DTO;

class UserDTO implements DTO
{
    private string $username;
    private string $password;
    private string $email;

    /**
     * @param string $username
     * @param string $password
     * @param string $email
     */
    public function __construct(string $username, string $password, string $email)
    {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
```
在控制器层需要继承扩展的`BaseController`, 代码如下：
```php 
<?php

namespace App\Http\Controllers;

use App\Models\DTO\DelUserDTO;
use App\Models\DTO\UserDTO;
use App\Models\User;
use AutoDTO\Helper\Http\Controller\BaseController;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    public function create(UserDTO $dto)
    {
       return User::factory()->create([
           "name" => $dto->getUsername(),
           "password" => Hash::make($dto->getPassword())
       ]);
    }

    public function del(DelUserDTO $dto)
    {
        return User::destroy($dto->getUserId());
    }
}
```
每个方法都可以自定义自己的DTO。
