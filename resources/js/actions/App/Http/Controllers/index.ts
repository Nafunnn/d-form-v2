import Dashboard from './Dashboard'
import FeaturePageController from './FeaturePageController'
import EventsController from './EventsController'
import DocsPageController from './DocsPageController'
import Auth from './Auth'

const Controllers = {
    Dashboard: Object.assign(Dashboard, Dashboard),
    FeaturePageController: Object.assign(FeaturePageController, FeaturePageController),
    EventsController: Object.assign(EventsController, EventsController),
    DocsPageController: Object.assign(DocsPageController, DocsPageController),
    Auth: Object.assign(Auth, Auth),
}

export default Controllers