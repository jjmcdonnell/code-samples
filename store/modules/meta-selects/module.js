import state from './state.js';
import actions from './actions.js';
import getters from './getters.js';
import mutations from './mutations.js';

const MetaSelects = {};
MetaSelects.state = state;
MetaSelects.actions = actions;
MetaSelects.mutations = mutations;
MetaSelects.getters = getters;
export default MetaSelects;