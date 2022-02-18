<template>
	<n-space vertical>
		<n-select filterable tag :list-name="listName" :options="list"/>
	</n-space>
</template>

<script>
import { defineComponent } from 'vue'
import { mapState, mapActions, mapGetters } from 'vuex'
import { useMessage } from 'naive-ui'
import { NSelect, NSpace } from 'naive-ui'

export default defineComponent({
	name: 'MetaSelectsList',
	props: ['listName', 'list'],
	components:{
		NSelect, NSpace
	},
	computed: {
		...mapGetters({
			getList: 'getList'
		}),
		...mapState({}),
	},
	created() {
		this.$store.dispatch("loadList", this.listName);
		this.list = this.getList(this.listName);
	},
})
</script>