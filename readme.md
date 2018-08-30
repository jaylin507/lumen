# Extend Lumen PHP Framework

## 目录改动

- app/Http下设置为多客户端（除Common外）
    - 每一个客户端都拥有自己的控制器、模型、路由、验证器
    - 继承的类统一放在Common中
    - 所有模型统一继承Common模型
    
- 项目根目录下增加config目录，并在bootstrap/app.php中注册

- app/Http/Common/Controllers目录中添加控制器基类，定义了API返回格式
- app/Http/Common/Models目录中添加模型基类，定义了数据分页
- app/Http/Common/Validator目录中添加验证器基类
    - 通用验证器也放在此目录
    - 通用验证器建议验证数据不要过多，非共性数据不建议放在一起

- app/ 目录下新增Libraries目录
    - 增加日志记录系统
    
- app/Handler 异常类优化，抛出json异常


## 引用的compose扩展包

- monolog/monolog 日志
- swiftmailer/swiftmailer 邮件服务
- guzzlehttp/guzzle：HTTP客户端，构造请求


## 开发规范
### 遵循原则
1. 尽可能复用代码，尽可能统一规范
1. 控制器不能有任何操作数据库操作，统一命名，有异议可以一起讨论
1. 将重的业务逻辑放到 `service`
1. 所有数据库相关操作放到 `model`，`model` 根据实际情况拆分
    - Model.php：模型
    - Common/BaseModel：实体定义，常用筛选

### 命名规范（参考laravel路由resource规范）
类型 | Verb | Route | Controller | Repository/Model
|:----:|:---:|----|:---:|:---:|
查询列表 | GET | /photos | index | list
获取单个 | GET | /photo/{id} | show | find
增加单个 | POST | /photo | create | add
增加多个 | POST | /photos | store | store
修改单个 | POST | /photos/{id}/edit | edit | edit
删除单个 | POST | /photos/{id} | destroy | destroy