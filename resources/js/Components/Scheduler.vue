<template>
  <DayPilotScheduler :config="config" ref="schedulerRef" />
</template>

<script setup>
import { DayPilot, DayPilotScheduler } from 'daypilot-pro-vue'
import { ref, reactive, onMounted } from 'vue'


// Define props
const props = defineProps({
  events: {
    type: Array,
    required: true,
  },
  resources: {
    type: Array,
    required: true,
  },
})

const config = reactive({
  timeHeaders: [{ groupBy: 'Month' }, { groupBy: 'Day', format: 'd' }],
  scale: 'Day',
  days: DayPilot.Date.today().daysInYear(),
  startDate: DayPilot.Date.today().firstDayOfYear(),
  timeRangeSelectedHandling: 'Enabled',
  eventHeight: 40,
  onTimeRangeSelected: async (args) => {
    const dp = schedulerRef.value?.control
    const modal = await DayPilot.Modal.prompt('Create a new event:', 'Event 1')
    dp.clearSelection()
    if (modal.canceled) {
      return
    }
    dp.events.add({
      start: args.start,
      end: args.end,
      id: DayPilot.guid(),
      resource: args.resource,
      text: modal.result
    })
  },
  eventMoveHandling: 'Update',
  onEventMoved: (args) => {
    args.control.message('Event moved: ' + args.e.data.text)
  },
//   eventResizeHandling: 'Update',
//   onEventResized: (args) => {
//     args.control.message('Event resized: ' + args.e.data.text)
//   },
//   eventClickHandling: 'Enabled',
//   onEventClicked: (args) => {
//     args.control.message('Event clicked: ' + args.e.data.text)
//   },
  eventHoverHandling: 'Disabled',
  treeEnabled: true,
  onBeforeEventRender: (args) => {
    args.data.barColor = args.data.color
    args.data.areas = [
      {
        top: 12,
        right: 6,
        width: 20,
        height: 20,
        symbol: 'icons/daypilot.svg#minichevron-down-4',
        fontColor: '#999',
        visibility: 'Hover',
        action: 'ContextMenu',
        padding: 1,
        style:
          'background-color: #f9f9f9; border: 2px solid #ccc; cursor:pointer; border-radius: 50%;'
      }
    ]
  },
  contextMenu: new DayPilot.Menu({
    items: [
      {
        text: 'Delete',
        onClick: (args) => {
          const e = args.source
          const dp = schedulerRef.value?.control
          dp.events.remove(e)
          dp.message('Deleted.')
        }
      },
      {
        text: '-'
      },
      {
        text: 'Blue',
        icon: 'icon icon-blue',
        color: '#1155cc',
        onClick: (args) => {
          updateColor(args.source, args.item.color)
        }
      },
      {
        text: 'Green',
        icon: 'icon icon-green',
        color: '#6aa84f',
        onClick: (args) => {
          updateColor(args.source, args.item.color)
        }
      },
      {
        text: 'Yellow',
        icon: 'icon icon-yellow',
        color: '#f1c232',
        onClick: (args) => {
          updateColor(args.source, args.item.color)
        }
      },
      {
        text: 'Red',
        icon: 'icon icon-red',
        color: '#cc0000',
        onClick: (args) => {
          updateColor(args.source, args.item.color)
        }
      }
    ]
  })
})
const schedulerRef = ref(null)


const updateColor = (e, color) => {
  const dp = schedulerRef.value?.control
  e.data.color = color
  dp.events.update(e)
  dp.message('Color updated')
}

onMounted(() => {
  const dp = schedulerRef.value?.control

  config.events = props.events
  config.resources = props.resources

  dp.message('Welcome!')
  dp.scrollTo(DayPilot.Date.today().firstDayOfMonth())
})
</script>
