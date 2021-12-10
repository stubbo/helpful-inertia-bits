## Installing
Download these files and paste them into the root of your project.

## Creating InertiaJS and API route
You can use `Route::inertiaApi($name, $controller)` just like you would `Route::resource(...)`

## Adding middleware
```php
Route::inertiaApi('users', UserController::class, middleware: [
    'api' => ['auth:sanctum'],
    'web' => ['auth'],
]);
```

## Handling Responses
```php
class SSHController extends Controller
{
    public function index(): AnonymousResourceCollection|Response
    {
        $users = User::all();

        // $this->handleResponse($data, $component)
        // because there is only one array key we will return AnonymousResourceCollection for api calls
        return $this->handleResponse(compact('users'), 'Users');
    }

    public function store(CreateUserRequest $request): RedirectResponse|JsonResource
    {
        $user = User::create($request->validated());
        
        // $this->handleRedirect($data)
        // will call back() for inertia
        // because data is a resource we get JsonResource for api calls, collections and arrays with one key return AnonymousResourceCollection
        return $this->handleRedirect($key);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse|JsonResource
    {
        $user = $user->update($request->validated());
        
        // both handleResponse and handleRedirect allow you change how api's return
        return $this->handleRedirect($key, apiCallback: function() use ($user) {
            return new UserResource($user)
        });
    }
}
```