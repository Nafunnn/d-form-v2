import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
import events735790 from './events'
/**
* @see routes/web/admin/index.php:17
* @route '/dashboard/user/events'
*/
export const events = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: events.url(options),
    method: 'get',
})

events.definition = {
    methods: ["get","head"],
    url: '/dashboard/user/events',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web/admin/index.php:17
* @route '/dashboard/user/events'
*/
events.url = (options?: RouteQueryOptions) => {
    return events.definition.url + queryParams(options)
}

/**
* @see routes/web/admin/index.php:17
* @route '/dashboard/user/events'
*/
events.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: events.url(options),
    method: 'get',
})

/**
* @see routes/web/admin/index.php:17
* @route '/dashboard/user/events'
*/
events.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: events.url(options),
    method: 'head',
})

const user = {
    events: Object.assign(events, events735790),
}

export default user