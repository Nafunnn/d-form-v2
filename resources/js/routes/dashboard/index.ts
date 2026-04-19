import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import events from './events'
import user from './user'
/**
* @see \App\Http\Controllers\Dashboard\HomeController::__invoke
* @see Http/Controllers/Dashboard/HomeController.php:18
* @route '/dashboard'
*/
export const home = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

home.definition = {
    methods: ["get","head"],
    url: '/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Dashboard\HomeController::__invoke
* @see Http/Controllers/Dashboard/HomeController.php:18
* @route '/dashboard'
*/
home.url = (options?: RouteQueryOptions) => {
    return home.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Dashboard\HomeController::__invoke
* @see Http/Controllers/Dashboard/HomeController.php:18
* @route '/dashboard'
*/
home.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Dashboard\HomeController::__invoke
* @see Http/Controllers/Dashboard/HomeController.php:18
* @route '/dashboard'
*/
home.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(options),
    method: 'head',
})

/**
* @see routes/web/admin/index.php:14
* @route '/dashboard/profile'
*/
export const profile = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(options),
    method: 'get',
})

profile.definition = {
    methods: ["get","head"],
    url: '/dashboard/profile',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web/admin/index.php:14
* @route '/dashboard/profile'
*/
profile.url = (options?: RouteQueryOptions) => {
    return profile.definition.url + queryParams(options)
}

/**
* @see routes/web/admin/index.php:14
* @route '/dashboard/profile'
*/
profile.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(options),
    method: 'get',
})

/**
* @see routes/web/admin/index.php:14
* @route '/dashboard/profile'
*/
profile.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: profile.url(options),
    method: 'head',
})

const dashboard = {
    events: Object.assign(events, events),
    home: Object.assign(home, home),
    profile: Object.assign(profile, profile),
    user: Object.assign(user, user),
}

export default dashboard