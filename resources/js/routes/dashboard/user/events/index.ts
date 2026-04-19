import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see routes/web/admin/index.php:31
* @route '/dashboard/user/events/{event}'
*/
export const show = (args: { event: string | { id: string } } | [event: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/dashboard/user/events/{event}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web/admin/index.php:31
* @route '/dashboard/user/events/{event}'
*/
show.url = (args: { event: string | { id: string } } | [event: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { event: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { event: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            event: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
    }

    return show.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see routes/web/admin/index.php:31
* @route '/dashboard/user/events/{event}'
*/
show.get = (args: { event: string | { id: string } } | [event: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see routes/web/admin/index.php:31
* @route '/dashboard/user/events/{event}'
*/
show.head = (args: { event: string | { id: string } } | [event: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see routes/web/admin/index.php:37
* @route '/dashboard/user/events/{event}/register'
*/
export const register = (args: { event: string | { id: string } } | [event: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(args, options),
    method: 'get',
})

register.definition = {
    methods: ["get","head"],
    url: '/dashboard/user/events/{event}/register',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web/admin/index.php:37
* @route '/dashboard/user/events/{event}/register'
*/
register.url = (args: { event: string | { id: string } } | [event: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { event: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { event: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            event: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
    }

    return register.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see routes/web/admin/index.php:37
* @route '/dashboard/user/events/{event}/register'
*/
register.get = (args: { event: string | { id: string } } | [event: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(args, options),
    method: 'get',
})

/**
* @see routes/web/admin/index.php:37
* @route '/dashboard/user/events/{event}/register'
*/
register.head = (args: { event: string | { id: string } } | [event: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: register.url(args, options),
    method: 'head',
})

const events = {
    show: Object.assign(show, show),
    register: Object.assign(register, register),
}

export default events